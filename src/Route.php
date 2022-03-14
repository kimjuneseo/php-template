<?php

function init($pages) {
  // 현재 url 가져옴 /test/1?idx=1에서 querystirng 분리
  [$url] = explode("?", $_SERVER['REQUEST_URI']);

  // web.php에서 세팅한 page 루프 돔
  foreach($pages as $p){
    // @ 기준으로 나눠서 path, controller 이름, class 내부 method 이름 가져옴
    [$path, $name, $method] = explode("@", $p);

    // regex escape 처리
    // /test/:id => /^\/test\/([^/]+)$/ 이렇게 변경됨
    $reg = preg_replace("/:([^\/])+/","([^/]+)", $path);
    $reg = preg_replace("/\//","\\/", $reg);
    $reg = "/^".$reg."$/";
    
    // /^\/test\/([^/]+)$/는 /test/1234에 매칭됨
    // preg_match의 세번째 인자인 $r은 매칭값을 가져옴 ([$fullPath, $gorup1, $group2, ...])
    // 위 test 기준으로는 ['/test/1234', '1234'] 이렇게 내려오는거
    if(preg_match($reg, $url, $r)){
      // Controller path
      $conName = "src\\Controller\\{$name}";

      $con = new $conName();

      // preg_match의 값을 파라미터로 넘겨줌
      $con->{$method}($r);
      exit;
    }
  }

  http_response_code(404);
}

// Route::get, Route::post를 실행했을때 $name으로 get, post가 들어가고 $args로 파라미터 값들이 들어감
function call($name, $args){
  // 페이지에 접근한 메소드가 동일할때
  if($_SERVER['REQUEST_METHOD'] == $name){
    init($args);
  }
}

$get = fn(...$args) => call("GET", $args);
$post = fn(...$args) => call("POST", $args);
