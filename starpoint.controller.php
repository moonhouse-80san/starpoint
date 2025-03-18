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
	public function procStarpointDoRateDocument(){
		//평가할 게시글의 번호와 평가 점수를 불러옵니다.
		//이때 star_srl은 숫자만 필터링 되기 위한 꼼수(?) 입니다.
		$document_srl = Context::get('doc_srl');
		$star_srl = Context::get('star_srl');
		$args->regdate = date('YmdHis');

		if(!$document_srl || !$star_srl){
			return new BaseObject(-1, '잘못 된 요청입니다');
		}

		if ($star_srl < 1 || $star_srl > 10) {
			//점수가 10이상이거나 0점이면 잘못된 점수입니다.
			return new BaseObject(-1, '잘못 된 점수입니다');
		}
		
		//게시글 번호와, 로그인 정보가 있다면 DB에 데이터를 넣습니다.
		//가볍게 만들었음으로, 게시글이 실제로 있는지는 체크하지 않습니다.
		$oStarPoint = getModel('starpoint');
		
		//이미 평점을 매겼었는지 체크합니다.
		$isRated = $oStarPoint->getIsRated($document_srl);

		if($isRated){
			//이미 평가하여 데이터가 있는 경우 
			return new BaseObject(-1, '이미 평가하셨습니다');
		}else{
			//평가 한 데이터가 없는 경우
			$oStarPoint->insertStarRate($document_srl, $star_srl);
		}

		return false;
	}
}
