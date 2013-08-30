<?php

class m130830_232105_alter_file_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('file', 'hash', 'VARCHAR(255) NOT NULL AFTER `byteSize`');
	}

	public function down()
	{
		$this->dropColumn('file', 'hash');
	}
}