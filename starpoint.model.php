<?php

/**
 * 게시글 별점리뷰
 *
 * Copyright (c) ICETEA
 *
 * Generated with https://www.poesis.dev/tools/modulegen
 */
class StarpointModel extends Starpoint
{

	public static function getIsRated($document_srl){
		//회원 정보와 게시글 번호를 기준으로 이미 기존에 평점을 남겼는지 체크 합니다.

		$logged_info = Context::get('logged_info');
		if(!$logged_info){
			return new BaseObject(-1, '로그인이 필요합니다.');
		}

		$args = new stdClass();
		$args->member_srl = $logged_info->member_srl;
		$args->document_srl = $document_srl;

		$output = executeQuery('starpoint.getRated', $args);

		return $output->data;

	}

	public static function getDocumentRatedTotalPoint($document_srl){
		//게시글에 있는 전체 평점을 평균을 내어 가져옵니다.

		$args = new stdClass();
		$args->document_srl = $document_srl;

		$total_point = executeQuery('starpoint.getDocumentRatedTotalPoint', $args);
		return $total_point->data;
	}

	public static function insertStarRate($document_srl, $star_srl){
		$logged_info = Context::get('logged_info');
		if(!$logged_info){
			return new BaseObject(-1, '로그인이 필요합니다.');
		}

        $args = new stdClass();
        $args->document_srl = $document_srl;
        $args->member_srl = $logged_info->member_srl;
        $args->star_rate = $star_srl;
        $args->regdate = date('YmdHis'); // 현재 시간을 YYYYMMDDhhmmss 형식으로 저장

		$result = executeQuery('starpoint.insertStarRate', $args);

		return $result;
	}

	/**
	 * @brief 사용자가 평가한 별점 정보 가져오기
	 * @param int $document_srl 문서 일련번호
	 * @param int $member_srl 회원 일련번호
	 * @return object 별점 정보
	 */
	public function getUserRating($document_srl, $member_srl = null) {
		if (!$member_srl) {
			$logged_info = Context::get('logged_info');
			if (!$logged_info) return null;
			$member_srl = $logged_info->member_srl;
		}

		$args = new stdClass();
		$args->document_srl = $document_srl;
		$args->member_srl = $member_srl;

		$output = executeQuery('starpoint.getRatingByMember', $args);
		if (!$output->toBool() || !$output->data) return null;
		
		return $output->data;
	}

	/**
	 * @brief 문서의 평점 통계 정보 가져오기
	 * @param int $document_srl 문서 일련번호
	 * @return object 평점 통계 정보
	 */
	public function getStarPointStatistics($document_srl) {
		$args = new stdClass();
		$args->document_srl = $document_srl;

		// 평균 평점과 평가 수 가져오기
		$output = executeQuery('starpoint.getRatingStatistics', $args);
		if (!$output->toBool()) return $output;

		return $output->data;
	}

	/**
	 * @brief 모듈 설정 가져오기
	 * @return object 모듈 설정 객체
	 */
	public function getConfig() {
		static $config = null;
		if(is_null($config)) {
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('starpoint');
			if(!$config) {
				$config = new stdClass();
			}
			
			// 기본값 설정
			if(!isset($config->target_mid)) $config->target_mid = array();
		}
		return $config;
	}
	
	/**
	 * @brief 해당 모듈이 별점 사용이 가능한지 확인
	 * @param int $module_srl 모듈 일련번호
	 * @return bool 별점 사용 가능 여부
	 */
	public function isStarpointEnabled($module_srl) {
		$config = $this->getConfig();
		return in_array($module_srl, $config->target_mid);
	}
}