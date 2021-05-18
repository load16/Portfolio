//Класс для дописывания кода в элемент.

var addQuery = {};                                 //Создаем класс запроса AJAX.
$(document).ready(function(){
    
})//Конец главной функции.

addQuery.add = function(ControlId, addId){
	ElemetHTML = '#'+ControlId;
	addHTML = '#'+addId;
    OutData = $(ElemetHTML);                    //Отбор элемента.
    addData = $(addHTML);                    //Отбор элемента.
    HtmlCode = addData.html()+OutData.html();
    addData.html(HtmlCode);                     //Замена элемента.
	//alert('тик');
}