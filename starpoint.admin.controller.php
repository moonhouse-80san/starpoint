<?php

/**
 * 게시글 별점리뷰
 *
 * Copyright (c) ICETEA
 *
 * 관리자 컨트롤러 클래스
 */
class StarpointAdminController extends Starpoint
{
    /**
     * @brief 모듈 설정 저장
     */
    function procStarpointAdminInsertConfig()
    {
        // 모듈 정보 가져오기
        $vars = Context::getRequestVars();
        
        // 설정 객체 생성
        $config = new stdClass();
        
        // 대상 모듈 처리
        $config->target_modules = $vars->target_modules;
        if(!is_array($config->target_modules)) {
            $config->target_modules = array();
        }
        
        // 설정 저장
        $oModuleController = getController('module');
        $output = $oModuleController->insertModuleConfig('starpoint', $config);
        if(!$output->toBool()) {
            return $output;
        }
        
        // 캐시 재생성
        $this->recompileCache();
        
        // 메시지 설정 및 리다이렉트
        $this->setMessage('성공적으로 저장했습니다.');
        $this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispStarpointAdminConfig'));
        
        return new BaseObject();
    }
    
    /**
     * @brief 캐시 재생성
     */
    function recompileCache()
    {
        // 캐시 파일 삭제
        FileHandler::removeFilesInDir('./files/cache/module_info');
    }
}