<?php

namespace simply_static_pro;

use Exception;
use phpseclib3\Net\SFTP;
use Simply_Static;
use Simply_Static\Options;
use Simply_Static\Page;
use Simply_Static\Util;

/**
 * Class which handles SFTP Deployment.
 */
class SFTP_Deploy_Task extends Simply_Static\Task {
	/**
	 * The task name.
	 *
	 * @var string
	 */
	protected static $task_name = 'sftp_deploy';

	/**
	 * Given start time for the export.
	 *
	 * @var string
	 */
	private $start_time;

	/**
	 * Get SFTP
	 * @var null|SFTP
	 */
	protected $sftp = null;

	/**
	 * Temp directory.
	 *
	 * @var string
	 */
	private string $temp_dir;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$options          = Options::instance();
		$this->options    = $options;
		$this->temp_dir   = $options->get_archive_dir();
		$this->start_time = $options->get( 'archive_start_time' );
	}

	public function get_start_time() {
		$this->start_time = get_transient( 'ssp_sftp_deploy_start_time' );

		if ( ! $this->start_time ) {
			$start = Util::formatted_datetime();
			set_transient( 'ssp_sftp_deploy_start_time', $start, 0 );
			$this->start_time = $start;
		}

		return $this->start_time;
	}

	/**
	 * Push a batch of files from the temp dir to SFTP folders.
	 *
	 * @return boolean true if done, false if not done.
	 * @throws Exception When the GitHub API returns an error.
	 */
	public function perform(): bool {
		$this->get_start_time();

		$sftp = $this->get_sftp();
		if ( ! $sftp ) {
			$this->save_status_message( __( 'We could not authenticate with SFTP. Stopping SFTP upload.', 'simply-static' ) );
			return true; // Returning TRUE to stop this task.
		}

		list( $pages_processed, $total_pages ) = $this->upload_static_files( $this->temp_dir );

		// return true when done (no more pages).
		if ( $pages_processed >= $total_pages ) {
			$message = sprintf( __( 'Uploaded %d of %d pages/files', 'simply-static' ), $pages_processed, $total_pages );
			$this->save_status_message( $message );

			// Removing cached time.
			delete_transient( 'ssp_sftp_deploy_start_time' );

			do_action( 'ssp_finished_sftp_transfer', $this->temp_dir );
		}

		return $pages_processed >= $total_pages;
	}

	public function get_sftp() {
		if ( $this->sftp === null ) {
			$host   = $this->options->get( 'sftp_host' );
			$user   = $this->options->get( 'sftp_user' );
			$pass   = $this->options->get( 'sftp_pass' );
			$port   = $this->options->get( 'sftp_port' );

			if ( ! $port ) {
				$port = 22;
			}

			if ( strpos( $host, 'sftp://' ) === 0 ) {
				$host = str_replace( 'sftp://', '', $host );
			}

			$this->sftp  = new SFTP( $host, absint( $port ) );
			$login = $this->sftp->login($user, $pass);

			if ( ! $login ) {
				Util::debug_log( 'Not able to login to SFTP' );
				return false;
			}

		}

		return $this->sftp;
	}

	/**
	 * Upload files to DO Spaces.
	 *
	 * @param string $destination_dir The directory to put the files.
	 *
	 * @return array
	 * @throws Exception When the upload fails.
	 */
	public function upload_static_files( string $destination_dir ): array {
		$batch_size       = apply_filters( 'ssp_sftp_batch_size', 250 );
		$throttle_request = apply_filters( 'ssp_throttle_sftp_request', true );

		// last_modified_at > ? AND.
		$static_pages    = Page::query()
		                       ->where( "file_path IS NOT NULL" )
		                       ->where( "file_path != ''" )
		                       ->where( "( last_transferred_at < ? OR last_transferred_at IS NULL )", $this->start_time )
		                       ->limit( $batch_size )
		                       ->find();
		$pages_remaining = count( $static_pages );
		$total_pages     = Page::query()
		                       ->where( "file_path IS NOT NULL" )
		                       ->where( "file_path != ''" )
		                       ->count();

		$pages_processed = $total_pages - $pages_remaining;
		Util::debug_log( 'Total pages: ' . $total_pages . '; Pages remaining: ' . $pages_remaining );

		$sftp   = $this->get_sftp();
		$folder = $this->options->get( 'sftp_folder' );

		if ( $folder ) {
			$folder = trailingslashit( $folder );
		}

		if ( $pages_processed !== 0 ) {
			// Showing message while uploading so users know what's happening
			$message = sprintf( __( 'Uploading %d of %d pages/files', 'simply-static' ), $pages_processed, $total_pages );
			$this->save_status_message( $message );
		}

		while ( $static_page = array_shift( $static_pages ) ) {
			$file_path = $this->temp_dir . $static_page->file_path;
			$upload_path = $folder .  $static_page->file_path;

			if ( ! is_dir( $file_path ) && file_exists( $file_path ) ) {
				// SFTP UPLOAD
				$upload = $sftp->put( $upload_path, $file_path, SFTP::SOURCE_LOCAL_FILE );

				// Maybe throttle request.
				if ( $throttle_request ) {
					sleep( 1 );
				}

				if ( ! $upload ) {
					continue;
				}
			}

			do_action( 'ssp_file_transferred_to_sftp', $static_page, $destination_dir );

			$static_page->last_transferred_at = Util::formatted_datetime();
			$static_page->save();
		}

		return array( $pages_processed, $total_pages );
	}

}
