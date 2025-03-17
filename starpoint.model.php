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
			return new BaseObject(-1, '잘못 된 요청입니다.');
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
			return new BaseObject(-1, '잘못 된 요청입니다.');
		}

        $args = new stdClass();
        $args->document_srl = $document_srl;
        $args->member_srl = $logged_info->member_srl;
        $args->star_rate = $star_srl;

        $result = executeQuery('starpoint.insertStarRate', $args);

        return false;

    }

}
