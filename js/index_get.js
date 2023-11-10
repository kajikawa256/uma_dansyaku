// //選択された値を取得
// var selectedValue = document.getElementById("pulldown_racedate").value;
// console.log("選択された値は：" + selectedValue);
// //XMLHttpRequestオブジェクトを作成
// var xhr = new XMLHttpRequest();
// var url = "index_call.php?racedate=" + selectedValue;
// var params = "selectedValue=" + selectedValue;

// xhr.open("GET", url, true);


// xhr.onreadystatechange = function () {
// if (xhr.readyState == 4 && xhr.status == 200) {
//     // サーバーからの応答を処理
//     console.log(xhr.responseText);
// }
// };
// xhr.send(params);

function buttonClick(){
    console.log("test");
    params.delete('raceplace');
}
