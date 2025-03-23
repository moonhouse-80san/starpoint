<?php

/**
 * 게시글 별점리뷰
 *
 * Copyright (c) ICETEA
 *
 * Generated with https://www.poesis.dev/tools/modulegen
 */
class StarpointController extends Starpoint
{
	/**
	 * @brief 게시글에 별점 평가하기
	 * @return object
	 */
	public function procStarpointDoRateDocument(){
		// 로그인 확인
		$logged_info = Context::get('logged_info');
		if (!$logged_info) {
			return new BaseObject(-1, '로그인이 필요합니다.');
		}

		// 평가할 게시글의 번호와 평가 점수를 불러옵니다.
		$document_srl = Context::get('doc_srl');
		$star_srl = Context::get('star_srl');

		if(!$document_srl || !$star_srl){
			return new BaseObject(-1, '잘못된 요청입니다.');
		}

		// 별점은 1~5점 사이만 허용
		if ($star_srl < 1 || $star_srl > 5) {
			return new BaseObject(-1, '별점은 1~5점 사이여야 합니다.');
		}
		
		// 게시글 존재 확인
		$oDocumentModel = getModel('document');
		$oDocument = $oDocumentModel->getDocument($document_srl);
		if (!$oDocument->isExists()) {
			return new BaseObject(-1, '존재하지 않는 게시글입니다.');
		}

		// 별점 모델 불러오기
		$oStarPoint = getModel('starpoint');
		
		// 이미 평점을 매겼었는지 체크합니다.
		$isRated = $oStarPoint->getIsRated($document_srl);

		if($isRated){
			// 이미 평가한 경우 업데이트
			return new BaseObject(-1, '이미 평가하셨습니다.');
		} else {
			// 평가 한 데이터가 없는 경우 새로 추가
			$result = $oStarPoint->insertStarRate($document_srl, $star_srl);
			if (!$result->toBool()) {
				return $result;
			}
		}

		// AJAX 요청이면 평점 정보 반환
		$this->add('star_rate', $star_srl);
		
		$stats = $oStarPoint->getStarPointStatistics($document_srl);
		if ($stats) {
			$this->add('avg_rating', $stats->avg_rating);
			$this->add('total_ratings', $stats->total_ratings);
		}

		return new BaseObject();
	}

	/**
	 * @brief 별점 평가 삭제하기
	 * @return object
	 */
	public function procStarpointDeleteRating() {
		// 로그인 확인
		$logged_info = Context::get('logged_info');
		if (!$logged_info) {
			return new BaseObject(-1, '로그인이 필요합니다.');
		}

		$document_srl = Context::get('document_srl');
		if (!$document_srl) {
			return new BaseObject(-1, '잘못된 요청입니다.');
		}

		// 평가 정보 삭제
		$args = new stdClass();
		$args->document_srl = $document_srl;
		$args->member_srl = $logged_info->member_srl;

		$output = executeQuery('starpoint.deleteRating', $args);
		if (!$output->toBool()) {
			return $output;
		}

		// 삭제 후 통계 업데이트
		$oStarPoint = getModel('starpoint');
		$stats = $oStarPoint->getStarPointStatistics($document_srl);
		if ($stats) {
			$this->add('avg_rating', $stats->avg_rating);
			$this->add('total_ratings', $stats->total_ratings);
		}

		return new BaseObject();
	}

	/**
	 * @brief 문서 출력 시 별점 정보 표시 트리거
	 * @param object $obj 문서 객체
	 * @return object $obj
	 */
	function triggerGetDocumentEnd(&$obj) {
		// 모듈 설정 확인
		$oStarpointModel = getModel('starpoint');
		if(!$oStarpointModel->isStarpointEnabled($obj->get('module_srl'))) {
			return new BaseObject();
		}
		
		// 별점 정보 가져오기
		$document_srl = $obj->document_srl;
		$stats = $oStarpointModel->getStarPointStatistics($document_srl);
		
		// 별점 정보 추가
		if($stats) {
			$obj->add('starpoint_avg', $stats->avg_rating);
			$obj->add('starpoint_count', $stats->total_ratings);
		} else {
			$obj->add('starpoint_avg', 0);
			$obj->add('starpoint_count', 0);
		}
		
		return new BaseObject();
	}
	
	/**
	 * @brief 문서 출력 시 별점 템플릿 추가 트리거
	 * @param object $obj 문서 객체
	 * @return object $obj
	 */
	function triggerShowDocument(&$obj) {
		// 모듈 설정 확인
		$oStarpointModel = getModel('starpoint');
		if(!$oStarpointModel->isStarpointEnabled($obj->module_srl)) {
			return new BaseObject();
		}
		
		// 별점 템플릿 로드
		$oTemplate = &TemplateHandler::getInstance();
		$tpl_path = _XE_PATH_ . 'modules/starpoint/tpl';
		$tpl_file = 'rating_include.html';
		
		$document_srl = $obj->document_srl;
		
		// 템플릿 변수 설정
		$logged_info = Context::get('logged_info');
		$is_logged = Context::get('is_logged');
		
		// 별점 정보 가져오기
		$stats = $oStarpointModel->getStarPointStatistics($document_srl);
		$rating_avg = $stats ? $stats->avg_rating : 0;
		$rating_count = $stats ? $stats->total_ratings : 0;
		
		// 사용자 평가 정보
		$user_rating = null;
		if($is_logged) {
			$user_rating = $oStarpointModel->getUserRating($document_srl, $logged_info->member_srl);
		}
		
		// 템플릿 변수 설정
		$tpl_vars = new stdClass();
		$tpl_vars->document_srl = $document_srl;
		$tpl_vars->rating_avg = $rating_avg;
		$tpl_vars->rating_count = $rating_count;
		$tpl_vars->is_logged = $is_logged;
		$tpl_vars->user_rating = $user_rating ? $user_rating->star_rate : 0;
		$tpl_vars->already_rated = $user_rating ? true : false;
		
		// 템플릿 출력
		$output = $oTemplate->compile($tpl_path, $tpl_file, $tpl_vars);
		
		// 문서 내용에 별점 템플릿 추가
		$obj->add('starpoint_rating', $output);
		
		return new BaseObject();
	}
}