<?php

/**
 * 게시글 별점리뷰 관리자 View
 *
 * Copyright 80san
 */
class StarpointAdminView extends Starpoint
{
	/**
	 * @brief 초기화
	 */
	public function init()
	{
		// 템플릿 경로 설정
		$this->setTemplatePath($this->module_path.'tpl');
	}

	/**
	 * @brief 별점 모듈 설정 페이지
	 */
	public function dispStarpointAdminConfig()
	{
		$board_list = array();
		Context::set('board_list', $board_list);
		$this->setTemplateFile('config');
	}
}