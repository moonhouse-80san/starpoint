<?php

/**
 * 게시글 별점리뷰
 *
 * Copyright (c) ICETEA
 *
 * 관리자 설정 화면 클래스
 */
class StarpointAdminView extends Starpoint
{
    /**
     * @brief 초기화
     */
    function init()
    {
        // 템플릿 경로 설정
        $this->setTemplatePath($this->module_path.'tpl/admin');
        
        // 관리자 모듈 스킨 설정
        $this->setLayoutPath('./modules/admin/tpl');
        $this->setLayoutFile('layout');
    }

    /**
     * @brief 관리자 설정 페이지
     */
    function dispStarpointAdminConfig()
    {
        // 모듈 목록 불러오기
        $oModuleModel = getModel('module');
        $module_list = $oModuleModel->getMidList(); // getMidList 함수로 변경
        
        // 현재 설정된 모듈 정보 가져오기
        $config = $this->getConfig();
        
        // 템플릿에 변수 설정
        Context::set('module_list', $module_list);
        Context::set('config', $config);
        
        // 템플릿 파일 지정
        $this->setTemplateFile('config');
        
        return new BaseObject();
    }

    /**
     * @brief 모듈 설정 가져오기
     */
    function getConfig()
    {
        // 모듈 설정 불러오기
        $oModuleModel = getModel('module');
        $config = $oModuleModel->getModuleConfig('starpoint');
        if(!$config) {
            $config = new stdClass();
        }
        
        // 기본값 설정
        if(!isset($config->target_modules) || !is_array($config->target_modules)) {
            $config->target_modules = array();
        }
        return $config;
    }
    
    /**
     * @brief 관리자 메인 페이지
     */
    function dispStarpointAdminIndex()
    {
        // 메인 페이지는 설정 페이지로 리다이렉트
        $this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispStarpointAdminConfig'));
        return new BaseObject();
    }
}