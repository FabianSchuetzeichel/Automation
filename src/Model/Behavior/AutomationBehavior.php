<?php
/**
 * @author: Fabian Schützeichel
 */

namespace Automation\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\Association;

/**
 * Automation behavior
 */
class AutomationBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function initialize(array $config){

    }

    public function autoFields($ignores = array(),$filter=array()){//filter not working until now
        //set defaultIgnores
        $defaultIgnores = [
            'id','created','modified'
        ];
        //merge with custom ignores
        $ignores = array_merge($defaultIgnores, $ignores);
        //get requested table
        $table = $this->_table;
        //empty result + associations
        $result = [];
        $associations=[];

        //generate associations
        foreach($table->associations()->keys() as $assKey){
            $associations[$table->association($assKey)->foreignKey()]=[
                'name'=>ucfirst($assKey),
                'association'=>$table->association($assKey)
            ];
        }

        //iterate default schema and analyze belongsTo like relations
        foreach($table->schema()->columns() as $colName){
            if(!in_array($colName,$ignores)){
                $colDetails=$table->schema()->column($colName);
                //default options
                $options=[
                    'label'=>ucfirst($colName),
                    'placeholder'=>$this->_aliasToPlaceholder($colName),
                ];
                if(array_key_exists($colName,$associations)){
                    //modify options
                    $options = [
                        'label'=>ucfirst($associations[$colName]['association']->alias()),
                        'placeholder'=>$this->_aliasToPlaceholder($associations[$colName]['association']->alias()),
                        'options'=>$associations[$colName]['association']->find('list')->toArray()
                    ];
                    unset($associations[$colName]);
                }else{

                }
                $result[$colName]['options']=$options;
            }

        }

        foreach($associations as $a){
            if(!in_array($a['association']->foreignKey(),$ignores)&&!in_array($a['name'],$ignores)){
                $options=[
                    #'name'=>'tags',
                    'label'=>ucfirst($a['association']->alias()),
                    'placeholder'=>$this->_aliasToPlaceholder($a['association']->alias()),
                    'options'=>$a['association']->find('list')->toArray(),
                    'multiple'=>true
                ];
                $result[lcfirst($a['name']).'._ids']['options']=$options;
            }

        }
        return $result;
    }

    private function _aliasToPlaceholder($alias){
        return ucfirst($alias).' hier eintragen';
    }
}
