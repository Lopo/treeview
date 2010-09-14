<?php
namespace ExamplesModule;
/**
 * Demos model.
 */

use Nette\Object;

class TreeViewModel
extends Object
{
	/** @var DibiConnection */
	private $db;

	public function __construct()
	{
		$this->db=\dibi::getConnection('sqlite');
	}

	private function query($sql)
	{
		return $this->db->query($sql);
	}

	public function getTree($src=FALSE)
	{
		$sql="SELECT"
				." *"
			." FROM"
				." treeview";
		if ($src)
			return $this->db->dataSource($sql);
		return $this->query($sql);
	}

	public function setCTree($data)
	{
		$this->query("UPDATE treeview SET cb=0");
		$this->query("UPDATE treeview SET cb=1 WHERE id IN (".implode(',', array_values($data)).")");
	}

	public function setETree($tree)
	{
		if (!is_array($tree))
			throw new Exception(__CLASS__.__FUNCTION__.' vyzaduje ako parameter pole');
		$i=1;
		return $this->setTree($tree);
		foreach ($tree as $node) {
			$this->setTree($node, $i);
			$i++;
			}
	}

	private function setTree($type, $position=1, $parent=NULL)
	{
		$i=1;
		foreach ($type as $id => $val) {
			if ($val!==NULL) {
				$this->setENode($id, $i, $parent);
				$this->setTree($val, $i, $id);
				}
			else
				$this->setENode($id, $i, $parent);
			$i++;
			}
	}

	private function setENode($id, $position=1, $parent=NULL)
	{
		$sql="UPDATE treeview"
			." SET"
				." parentId=".($parent!==NULL? $parent : "NULL").","
				." position=$position"
			." WHERE id=$id";
		return $this->query($sql);
	}

	public function addItem($parent=NULL, $name)
	{
		$sql="SELECT MAX(id)"
			." FROM treeview";
		$id=$this->db->fetchSingle($sql)+1;
		$sql="SELECT MAX(position)"
			." FROM treeview"
			." WHERE parentId".(($parent!==NULL && $parent!='NULL')? '='.$parent : ' IS NULL');
		$pos=$this->db->fetchSingle($sql)+1;
		$sql="INSERT INTO treeview"
				." (`id`, position`, `name`, `parentId`, `cb`)"
			." VALUES"
				." ($id, $pos, '$name', ".($parent!==NULL? $parent : 'NULL').", 1)";
		$this->query($sql);
		
	}

	public function delItem($id=NULL)
	{
		if ($id===NULL)
			throw new Exception('id musi byt zadane');
		if (!is_numeric($id))
			throw new Exception('id musi byt cislo');
		if (!is_int((int)$id))
			throw new Exception('id musi byt int');
		$sql="DELETE FROM treeview"
			." WHERE id=$id";
		$this->query($sql);
		$sql="SELECT id FROM treeview"
			." WHERE parentId=$id";
		foreach ($this->query($sql) as $row)
			$this->delItem($row['id']);
	}

	public function setCB($id=NULL, $ck=TRUE)
	{
		if ($id===NULL)
			return;
		return $this->query("UPDATE treeview SET cb=".($ck? 1 : 0)." WHERE id=$id");
	}

	public function setname($id, $val)
	{
		return $this->query("UPDATE treeview SET name='$val' WHERE id=$id");
	}
}