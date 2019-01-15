<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 29.11.18
 * Time: 09:51
 */

namespace Palasthotel\ProcessLog;

use Exception;

/**
 *
 */
class Database {

	/**
	 * @return \wpdb
	 */
	public static function wpdb() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * @return string
	 */
	public static function tablenameProcesses() {
		return self::wpdb()->prefix . "process_logs";
	}

	/**
	 * @return string
	 */
	public static function tablenameItems() {
		return self::wpdb()->prefix . "process_log_items";
	}

	/**
	 * @param int $count
	 * @param int $page
	 *
	 * @return array
	 */
	public function getProcessList( $count = 10, $page = 1 ) {

		$fields = array("process_id", "active_user", );
		$tablename = self::tablenameProcesses();
		$offset    = $count * ( $page - 1 );

		return self::wpdb()->get_results( self::wpdb()->prepare(
			"SELECT id, created, finished, location_url, hostname FROM $tablename ORDER BY process_id DESC LIMIT %d OFFSET %d", array($count, $offset)
		) );
	}

	/**
	 * @param int $pid
	 *
	 * @return array
	 */
	public function getProcessLogs( $pid ) {
		return $this->wpdb()->get_results(
			$this->wpdb()->prepare(
				"SELECT * FROM " . self::tablenameProcesses() . " WHERE process_id = %d",
				array( $pid )
			)
		);
	}

	/**
	 * @return Process|false
	 */
	public function nextProcess(){
		$process = new Process();
		$result = self::wpdb()->insert(
			self::tablenameProcesses(),
			$process->insertArgs()
		);
		if(!$result){
			return false;
		}
		$process->id = self::wpdb()->insert_id;
		return $process;
	}

	/**
	 * @param \Palasthotel\ProcessLog\ProcessLog $log
	 *
	 * @return false|int
	 */
	function addLog( ProcessLog $log ) {
		return self::wpdb()->insert(
			self::tablenameItems(),
			$log->insertArgs()
		);
	}


	/**
	 * create the tables if not exist
	 */
	function createTables() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$process = self::tablenameProcesses();
		dbDelta( "CREATE TABLE IF NOT EXISTS $process
		(
		 id bigint(20) unsigned auto_increment,
		 created DATETIME DEFAULT CURRENT_TIMESTAMP,
	
		 location_url varchar(255) comment 'where the event happend, url',
		 referer_url varchar(255),
		 hostname varchar(255),
		
		 primary key (id),
		 key (created),
		 key (hostname),
		 key (location_url),
		 key (referer_url)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" );

		$tablename = self::tablenameItems();
		
		dbDelta( "CREATE TABLE IF NOT EXISTS $tablename
		(
		 id bigint(20) unsigned auto_increment,
		 process_id bigint(20) unsigned,
		 created DATETIME DEFAULT CURRENT_TIMESTAMP,
		 
		 event_type varchar(100) NOT NULL,
		 active_user BIGINT,
		 message TEXT comment 'Message from code',
		 note TEXT comment 'Comment from user that triggered event. Comparable to git commit message',
		 comment TEXT comment 'after creation comments in backend',
		 severity varchar(100) NOT NULL,
		 link_url varchar(255) comment 'link to the result of the event',
		 location_path varchar(255) comment 'where the event happend, file system path',
		 affected_post BIGINT comment 'post id that was affected by the event',
		 affected_term BIGINT comment 'term id that was affected by the event',
		 affected_user BIGINT comment 'user id that was affected by the event',
		 affected_comment BIGINT comment 'comment id that was affected by the event',
		 expires BIGINT comment 'timestamp when to clean up this log entry',
		 
		 changed_data_field VARCHAR(255),
		 changed_data_values_old TEXT,
		 changed_data_values_new TEXT,
		 
		 variables text,
		 
		 blobdata BLOB,	
		 	 
		 primary key (id),
		 foreign key (process_id) REFERENCES $process(id) ,
		 key (created),
		 key (event_type),
		 key (active_user),
		 key (severity),
		 key (affected_post),
		 key (affected_term),
		 key (affected_user),
		 key (affected_comment),
		 key (expires),
		 key (changed_data_field)
		 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;" );
	}


}



