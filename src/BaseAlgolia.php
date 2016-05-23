<?php
namespace PMVC\PlugIn\algolia;

const INDEX_PATH = 'indexes';

class BaseAlgolia implements \ArrayAccess
{
    /**
     * Group ID
     */
    protected $groupId;
    /**
     * SSDB instance
     */
    public $db;

    /**
     * Construct
     */
    public function __construct($algolia, $groupId=null)
    {
        $this->db = $algolia;
        if (empty($groupId)) {
            return !trigger_error(
                'Need defined Algolia index name. ['.$groupId.']',
                E_USER_WARNING
            );
        } else {
            $this->groupId = $groupId;
        }
    }

    /**
     * Really name in database table name
     */
     public function getTable()
     {
        return $this->groupId;
     }

    /**
     * Command
     */
    public function getCommand($method=null, $params=array())
    {
        if (is_null($method)) {
            $method = 'GET';
        }
        $arr = array(
            CURLOPT_CUSTOMREQUEST=>$method
        );
        if (!empty($params)) {
            $arr = array_replace($arr,$params);
        }
        return $arr;
    }

    /**
     * get Post curl array
     */
     public function getPost($v)
     {
        $v = json_encode((object)$v); 
        return array (
           CURLOPT_POSTFIELDS => $v
        );
     }

    /**
     * Replace 
     */
     public function replace($k, $v)
     {
        if (is_null($k)) {
            $k = \PMVC\plug('guid')->gen();
        }
        $url = \PMVC\plug('url')->getUrl(INDEX_PATH);
        $url->set($this->groupId);
        $url->set($k);
        $doc = ['doc'=>$v];
        if (isset($v['_geoloc'])) {
            $doc['_geoloc'] = $v['_geoloc'];
        }
        $command = $this->getCommand(
            'PUT',
            $this->getPost($doc)
        );
        $result = \PMVC\plug('algolia')->request(
            $url,
            $command
        );
        return $result;
     }

     /**
      * Update
      */
     public function update($k, $v)
     {
            return $this->replace($k,$v);
     }

     /**
      * Search
      */
     public function search($query, array $params=[])
     {
         $url = \PMVC\plug('url')->getUrl(INDEX_PATH);
         $url->set($this->groupId);
         $default = [
            'analytics'=>false
         ];
         if (!empty($query)) {
            $default['query'] = $query;
         }
         $url->query = array_replace($default, $params);
         $result = \PMVC\plug('algolia')->request(
            $url
         );
         return $result;
     }


    /**
     * Exists
     *
     * @param string $k key 
     *
     * @return boolean
     */
    public function offsetExists($k)
    {
        $url = \PMVC\plug('url')->getUrl(INDEX_PATH);
        $url->set($this->groupId)
            ->set($k)
            ->set('?attributes=objectID');
        $result = \PMVC\plug('algolia')->request(
            $url
        );
        return 404 !== $result->code;
    }

    /**
     * Get
     *
     * @param mixed $k key
     *
     * @return mixed 
     */
    public function offsetGet($k=null)
    {
        $url = \PMVC\plug('url')->getUrl(INDEX_PATH);
        $url->set($this->groupId);
        $result = null;
        if (is_null($k)) {
	    $arr = $this->db->hgetall($this->groupId);
        } elseif (is_array($k)) { 
            $arr = $this->db->multi_hget($this->groupId, $k);
        } else {
            $url->set($k);
            $result = \PMVC\plug('algolia')->request(
                $url
            );
            if (!isset($result->body->doc)) {
                return null;
            } else {
                $result = (array)$result->body->doc;
            }
        }
        return $result;
    }

    /**
     * Set 
     *
     * @param mixed $k key
     * @param mixed $v value 
     *
     * @return bool 
     */
    public function offsetSet($k, $v=null)
    {
        if (is_null($k) || !isset($this[$k])) {
            return $this->replace($k, $v);
        } else {
            return $this->update($k, $v);
        }
    }

    /**
     * Clean
     *
     * @param mixed $k key
     *
     * @return bool 
     */
    public function offsetUnset($k=null)
    {
        if (empty($this->groupId)) {
            return;
        }
        return $this->db->hdel($this->groupId, $k);
    }
}
