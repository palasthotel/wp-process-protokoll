<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 2019-01-15
 * Time: 15:51
 */

namespace Palasthotel\ProcessLog;


/**
 * @property Writer writer
 */
class OptionsWatcher {
	public function __construct(Plugin $plugin) {
		$this->writer = $plugin->writer;
		add_action('added_option', array($this, 'added'),10,2);
		add_action('updated_option', array($this, 'updated'), 10, 3 );
		add_action('delete_option', array($this, 'delete'));
	}

	public function added($option, $value){
		$this->writer->addLog(
			ProcessLog::build()
			          ->setEventType( Plugin::EVENT_TYPE_CREATE )
			          ->setMessage( "added option" )
			          ->setChangedDataField( $option )
			          ->setChangedDataValueNew( $value )
		);
	}

	public function updated($option, $old_value, $value){
		$this->writer->addLog(
			ProcessLog::build()
			          ->setEventType( Plugin::EVENT_TYPE_UPDATE )
			          ->setMessage( "update option" )
			          ->setChangedDataField( $option )
			          ->setChangedDataValueOld( $old_value )
			          ->setChangedDataValueNew( $value )
		);
	}

	public function deleted($option){
		$this->writer->addLog(
			ProcessLog::build()
			          ->setEventType( Plugin::EVENT_TYPE_DELETE )
			          ->setMessage( "delete option" )
			          ->setChangedDataField( $option )
			          ->setChangedDataValueOld(get_option($option))
		);
	}
}