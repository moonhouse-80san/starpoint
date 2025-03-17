[설치 방법]

1. 다운 받은 모듈을 설치 - 관리자 설정 없음

2. 게시글 별 평점 모듈을 적용할 게시판 스킨의 파일에 아래의 코드를 작성 

(예 : /modules/board/skins/sketchbook5)

 

/modules/board/skins/sketchbook5/skin.xml 에 추가

<var name="star_point" type="radio">
    <title>게시글 별 평점 모듈 사용</title>
    <description>게시글 별 평점 모듈이 설치되어 있어야 합니다.</description>
    <options value="">
        <title>모듈 설치 안됨(기본)</title>
    </options>
    <options value="Y">
        <title>모듈 설치</title>
    </options>
</var>

 

/modules/board/skins/sketchbook5/_read.html 의 적당한 위치에 추가
<include cond="!$mi->star_point ==''" target="/modules/starpoint/tpl/rating.html" />

 

3. 게시판 스킨 설정에서 "게시글 별 평점 모듈 사용" 에서 "모듈 설치"를 선택하면 됩니다.
 

[비로그인 화면]

게시글 별 평점 모듈 - 문하우스 : image.png

 

[로그인 회원 화면]

게시글 별 평점 모듈 - 문하우스 : image.png

 

[평점후 화면]

게시글 별 평점 모듈 - 문하우스 : image.png
