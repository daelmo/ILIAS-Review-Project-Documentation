<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class for multiple choice tests.
 *
 * assReviewableMultipleChoice is a class that implements reviewable multiple choice questions.
 *
 * @extends assMultipleChoiceQuestion
 * 
 * @author		Julius Felchow <julius.felchow@mailbox.tu-dresden.de>
 * @author		Max Friedrich <max.friedrich@mailbox.tu-dresden.de>
 * 
 * @ingroup		ModulesTestQuestionPool
 */

require_once('./Modules/TestQuestionPool/classes/class.assMultipleChoice.php');
require_once('export/qti12/class.assReviewableMultipleChoiceExport.php');
require_once('import/qti12/class.assReviewableMultipleChoiceImport.php');

class assReviewableMultipleChoice extends assMultipleChoice {

	protected $taxonomy;
	protected $knowledge_dimension;


	/**
	 * assReviewableMultipleChoice constructor
	 *
	 * The constructor takes possible arguments an creates an instance of the assReviewableMultipleChoice object.
	 *
	 * @param string     $title       			A title string to describe the question
	 * @param string     $comment     			A comment string to describe the question
	 * @param string     $author      			A string containing the name of the questions author
	 * @param integer    $owner       			A numerical ID to identify the owner/creator
	 * @param string     $question    			The question string of the multiple choice question
	 * @param int|string $output_type 			The output order of the multiple choice answers
	 * @param int		 $taxonomy	  			The taxonomy of the question
	 * @param int		 $knowledge_dimension	The knowledge dimension of the question
	 */
	function _construct(
		$title = "", 
		$comment = "", 
		$author = "", 
		$owner = -1, 
		$question = "", 
		$output_type = OUTPUT_ORDER,
		$taxonomy = "",
		$knowledge_dimension = ""
	) {
		parent::_construct($title, $comment, $author, $owner, $question, $output_type);
		$this->taxonomy = $taxonomy;
		$this->knowledge_dimension = $knowledge_dimension;
	}
	
	/*
	 * @return string
	 */
	public function getQuestionType() {
		return "assReviewableMultipleChoice";
	}
	
	/*
	 * @return int $taxonomy
	 */
	public function getTaxonomy() {
		return $this->taxonomy;
	}
	
	/*
	 * @param int $a_taxonomy
	 */
	public function setTaxonomy($a_taxonomy) {
		$this->taxonomy = $a_taxonomy;
	}
	
	/*
	 * @return int $knowledge_dimension
	 */
	public function getKnowledgeDimension() {
		return $this->knowledge_dimension;
	}
	
	/*
	 * @param int $a_knowledge_dimension
	 */
	public function setKnowledgeDimension($a_knowledge_dimension) {
		$this->knowledge_dimension = $a_knowledge_dimension;
	}
	
	/*
	 * @return string
	 */
	function getReviewDataTable() {
		return "qpl_qst_rev_mc";
	}
	
	/**
	 * Function to save taxonomy and knowledge dimension to the database
	 * 
	 * @param string $original_id
	 */
	private function saveReviewDataToDb($original_id = "") {
		global $ilDB;
		
		$result = $ilDB->queryF(
			"SELECT * 
			FROM qpl_rev_qst 
			WHERE question_id = %s",
			array("integer"),
			array( $this->getId() ) 
		);
		
		if ($result->numRows() <= 0) {
			$affectedRows = $ilDB->insert(
				"qpl_rev_qst",
				array(
					"question_id"         => array( "integer"    , $this->getId()                 ),
					"taxonomy"            => array( "integer"    , $this->getTaxonomy()           ),
					"knowledge_dimension" => array( "integer"    , $this->getKnowledgeDimension() )
				)
			);
		} else {
			$affectedRows = $ilDB->update(
				"qpl_rev_qst", 
				array(
					"taxonomy"            => array( "integer"    , $this->getTaxonomy()           ),
					"knowledge_dimension" => array( "integer"    , $this->getKnowledgeDimension() )
				),
				array(
					"question_id"         => array( "integer" , $this->getId()                 )
				)
			);
		}
	}
	
	/**
	 * Overwritten function to save question to the database
	 * 
	 * @param string $original_id
	 */
	public function saveToDb($original_id = "") {
		$this->saveReviewDataToDb($original_id);
		parent::saveToDb($original_id);
	}
	
	/**
	 * Function to load taxonomy and knowledge dimension from the database
	 * 
	 * @param string $question_id	The id of target question
	 */
	private function loadReviewDataFromDb($question_id = "") {
		global $ilDB;
		
		$result = $ilDB->queryF(
			"SELECT taxonomy, knowledge_dimension FROM qpl_rev_qst WHERE question_id = %s",
			array("integer"),
			array($this->getId())
		);
		
		if($result->numRows() == 1) {
			$data = $ilDB->fetchAssoc($result);
			$this->setTaxonomy( $data['taxonomy'] );
			$this->setKnowledgeDimension( $data['knowledge_dimension'] );
		}
	}
	
	/**
	 * Function to load data from the database
	 * 
	 * @param string $question_id	The id of target question
	 */
	public function loadFromDb($question_id) {
		parent::loadFromDb($question_id);
		$this->loadReviewDataFromDb($original_id);
	}
	
	/**
	 * Function to delete the question
	 * 
	 * @param string $question_id	The id of target question
	 */
	function delete($question_id) {
		global $ilDB;
		
		$affectedRows = $ilDB->manipulate( "DELETE FROM qpl_rev_qst WHERE question_id = " . $question_id );
		
		parent::delete( $question_id );
	}
	
	/**
	 * Function to convert the question to JSONn
	 */
	function toJSON() {
		$result = json_decode( parent::toJson() );
		
		$result['taxonomy'] = $this->getTaxonomy();
		$result['knowlegde_dimension'] = $this->getKnowlegdgeDimension();
		
		return json_encode($result);
	}

}
