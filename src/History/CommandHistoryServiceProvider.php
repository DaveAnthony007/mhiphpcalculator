<?php

namespace Jakmall\Recruitment\Calculator\History;

use Illuminate\Contracts\Container\Container;
use Jakmall\Recruitment\Calculator\Container\ContainerServiceProviderInterface;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;
use Carbon\Carbon;
use PDO;
use PDOException;

class CommandHistoryServiceProvider implements ContainerServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $container): void
    {
        $container->bind(
            CommandHistoryManagerInterface::class,
            function () {
                //todo: register implementation
                return null;
            }
        );
    }

    private $pdo;

    protected $storageSqliteDbFile;
    protected $storageFile;

    public function __construct()
    {
        $this->storageSqliteDbFile = './storage/db.sqlite';
        $this->storageFile = './storage/db.txt';
        try {
            $this->pdo = new PDO("sqlite:" . $this->getStorageSqliteDbFile());
            
            // var_dump($this->pdo);
            if($this !== null) {
                $this->createTable();
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }
    protected function getStorageSqliteDbFile()
    {
        return $this->storageSqliteDbFile;
    }
    private function createTable(): void
    {
        $queryCreateTable = [
            'CREATE TABLE IF NOT EXISTS command_history (
                history_id INTEGER PRIMARY KEY,
                history_command TEXT NOT NULL,
                history_description TEXT NOT NULL,
                result  INTEGER NOT NULL,
                output TEXT NOT NULL,
                history_time TEXT NOT NULL
             )'
        ];
        foreach ($queryCreateTable as $query) {
            $this->pdo->exec($query);
        }
    }

    /**
     * @param string $history_command
     * @param string $history_description
     * @param int $result
     * @param string $output
     * @return string Last Table command_history id
     */
    private function saveHistoryToDB($obj) 
    {
        $sql = 'INSERT INTO command_history(history_command,history_description,result,output,history_time)'
            .'VALUES(:history_command,:history_description,:result,:output,:history_time)';
        $qryPdq = $this->pdo->prepare($sql);
        $qryPdq->bindValue(':history_command', $obj->command);
        $qryPdq->bindValue(':history_description', $obj->description);
        $qryPdq->bindValue(':result', $obj->result);
        $qryPdq->bindValue(':output', $obj->output);
        $qryPdq->bindValue(':history_time', date('Y-m-d H:i:s'));
        $qryPdq->execute();
        return $this->pdo->lastInsertId();
    }

    private function saveHistoryToFile($obj) 
    {
        $dataToWrite = $obj->command.'; '.$obj->description.'; '.$obj->result.'; '.$obj->output.'; '.date('Y-m-d H:i:s')."#";
        file_put_contents($this->getStorageFile(), $dataToWrite, FILE_APPEND | LOCK_EX);
    }

    /**
     * Insert a new history into command_history table
     * @param string $history_command
     * @param string $history_description
     * @param string $result
     * @param string $output
     */
    public function log($obj)
    {
        // var_dump($obj);
        try {
            $this->saveHistoryToDB($obj);
            $this->saveHistoryToFile($obj);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
        
    }

    /**
     * @return void
     */
    public function clearAll($id = null)
    {
        if(file_exists($this->storageSqliteDbFile)){
            unlink($this->storageSqliteDbFile);
        }

        if(file_exists($this->storageFile)){
            unlink($this->storageFile);
        }
    }

    /**
     * @return mixed
     */
    public function getStorageFile()
    {
        return $this->storageFile;
    }

    /**
     * @param string $storageFile file path of file
     */
    public function setStorageFile($storageFile): void
    {
        $this->storageFile = $storageFile;
    }

    /**
     * if need select all data just fill param with null or with empty array like this []
     * if need filter with some keyword fill that param with array ex. ['find_one', 'find_two']
     * @param array|null $filterArray
     * @return array
     */
    public function findAll($id = null) : array
    {
        $query = 'SELECT history_id, history_command, history_description, result, output, history_time FROM command_history';
        $pdoQry = $this->pdo
        ->query($query);
        $history = [];
        while ($row = $pdoQry->fetch(\PDO::FETCH_ASSOC)) {
            // var_dump($row['output']);
            $history[] = [
                'history_id' => $row['history_id'],
                'history_command' => $row['history_command'],
                'history_description' => $row['history_description'],
                'result' => $row['result'],
                'output' => $row['output'],
                'history_time' => Carbon::createFromFormat('Y-m-d H:i:s', $row['history_time'])
                ->format('Y-m-d H:i:s')
            ];
            // echo("something");
        }
        return $history;
    }

    /**
     * if need select all data just fill param with null or with empty array like this []
     * if need filter with some keyword fill that param with array ex. ['find_one', 'find_two']
     * @param array|null $filterArray
     * @return array
     */

    public function showFileHistory(array $filterArray = null)
    {
        $data = file_get_contents($this->getStorageFile());
        
        $data = str_replace(array("\n", "\r"), '', $data);
        $data = explode('#', $data);
        unset($data[count($data) - 1]); // delete/clear the last empty element

        $final_array = array();
        foreach($data as $key => $row){ // loop exploded data
            $exploded_item = explode('; ', $row);
            
            $final_array[] = [
                'history_command' => $exploded_item[0],
                'history_description' => $exploded_item[1],
                'result' => $exploded_item[2],
                'output' => $exploded_item[3],
                'history_time' => $exploded_item[4],
            ];
        }
        $item_collection = collect($final_array);

        if($filterArray != null){
            $item_collection = $item_collection->whereIn('history_command', $filterArray);
        } 

        return $item_collection;
    }

}
