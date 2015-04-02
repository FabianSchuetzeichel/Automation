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
            $cond = [];
            if(array_key_exists($table->association($assKey)->alias(),$filter)){
                $cond = $filter[$table->association($assKey)->alias()];
            }
            if($table->association($assKey)->type()=='manyToOne'){
                $caller = $table->association($assKey)->foreignKey();
            }else{
                $caller = $table->association($assKey)->alias();
            }
            $associations[$caller]=[
                'name'=>ucfirst($assKey),
                'association'=>$table->association($assKey),
                'listoptions'=>$table->association($assKey)->find('list')->where($cond)->toArray()
            ];
        }

        //iterate default schema and analyze belongsTo like relations
        foreach($table->schema()->columns() as $colName){
            if(!in_array($colName,$ignores)){
                $colDetails=$table->schema()->column($colName);
                //default options
                $options=[
                    'label'=>[
                        'text'=>ucfirst($colName)
                    ],
                    'placeholder'=>$this->_aliasToPlaceholder($colName),
                ];
                $ifoptions=[];
                if(array_key_exists($colName,$associations)){
                    //modify options
                    $ifoptions = [
                        'label'=>['text'=>ucfirst($associations[$colName]['association']->alias())],
                        'placeholder'=>$this->_aliasToPlaceholder($associations[$colName]['association']->alias()),
                        'options'=>$associations[$colName]['listoptions']
                    ];
                    unset($associations[$colName]);
                }elseif($colDetails['type']==='datetime'){
                    $ifoptions=[
                        'type'=>'text',
                        'class'=>'form-control datetimepick'
                    ];
                }elseif($colDetails['type']==='date'){
                    $ifoptions=[
                        'type'=>'text',
                        'class'=>'form-control datepick'
                    ];
                }elseif($colDetails['type']==='time'){
                    $ifoptions=[
                        'type'=>'text',
                        'class'=>'form-control timepick'
                    ];
                }
                $result[$colName]=array_replace_recursive($options,$ifoptions);
            }

        }

        foreach($associations as $a){
            if(!in_array($a['name'],$ignores)){
                $options=[
                    'label'=>['text'=>ucfirst($a['association']->alias())],
                    'placeholder'=>$this->_aliasToPlaceholder($a['association']->alias()),
                    'options'=>$a['listoptions'],
                    'multiple'=>true
                ];
                $result[lcfirst($a['name']).'._ids']=$options;
            }

        }
        return $result;
    }

    private function _aliasToPlaceholder($alias){
        return ucfirst($alias).' hier eintragen';
    }
}
