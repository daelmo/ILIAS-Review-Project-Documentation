<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

/**
* GUI, a row of checkboxes in the question-reviewer allocation matrix
*
* @var		array		$postvars		$_POST variables of each checkbox
* @var		integer	$question_id	id of the question corresponding to the matrix row
*
* @author Richard Mörbitz <Richard.Moerbitz@mailbox.tu-dresden.de>
*
* $Id$
*/

class ilCheckMatrixRowGUI extends ilCustomInputGUI {
	
	private $postvars;
	private $question_id;
	
	/**
	* Constructor for a line in a table-like display of ilSelectInputGUIs
	*
	* @param	array		$question		associative array = question record
	* @param	array		$reviewer_ids	ids of the reviewers belonging to each select input
	*/
	public function __construct($question, $reviewer_ids) {
		parent::__construct();
		global $tpl;
		$this->reviewer_ids = array();
		$this->question_id = $question["id"];
		foreach ($reviewer_ids as $reviewer_id)
			$this->postvars[$reviewer_id] = sprintf("id_%s_%s", $this->question_id, $reviewer_id);
		$path_to_il_tpl = ilPlugin::getPluginObject(IL_COMP_SERVICE, 'Repository', 'robj', 'Review')->getDirectory();
		$custom_tpl = new ilTemplate("tpl.matrix_row.html", true, true, $path_to_il_tpl);
		$tpl->addCss('./Customizing/global/plugins/Services/Repository/RepositoryObject/Review/templates/default/css/Review.css');
		foreach ($this->postvars as $postvar) {
			$chbox = new ilCheckboxInputGUI("", $postvar);
			$chbox->insert($custom_tpl);
		}
		$this->setTitle($question["title"]);
		$this->setHTML($custom_tpl->get());	
	}
	
	/**
	* Get the $_POST keys of this object´s input
	*
	* @return	array		$this->postvars		(reviewer id => $_POST key in the shape of "id_[question id]_[reviewer id])
	*/	
	public function getPostVars() {
		return $this->postvars;
	}
	
	/**
	* Get the question id belonging to this line of the matrix
	*
	* @return	integer	$this->question_id	question id
	*/
	public function getQuestionId() {
		return $this->question_id;
	}
}