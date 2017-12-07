<?php


use Phinx\Migration\AbstractMigration;

class InitialStructure extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('urls_visited_by_crawlers');
        $table->addColumn('url', 'string', [
            'length' => '1024',
        ]);

        $table->addIndex('url');
        $table->save();
    }
}
