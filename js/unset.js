//Класс отвественный за отключение элемента.
//$(document).ready(function(){
    var UnsetHTML = {};                                 //Создаем класс для удаления кода'.
    
    
    //Метод выполнения удаления HTML внутри элемента с кодом по ИД.
    UnsetHTML.UnsetId = function(IdElemets){
        ElemetHTML = '#'+IdElemets;
        OutData = $(ElemetHTML);                    //Отбор элемента.
        HtmlCode = '';
        OutData.html(HtmlCode); 
    }
    
    //Метод выполнения удаления HTML внутри элемента с кодом кода по CLASS.
    UnsetHTML.UnsetClass = function(ClassElemets){
        ElemetHTML = '.'+ClassElemets;
        OutData = $(ElemetHTML);                    //Отбор элемента.
        HtmlCode = '';
        OutData.html(HtmlCode); 
    }
    
    
    //Метод выполнения удаления элемента по CLASS.
    UnsetHTML.DellClassElemets = function(ClassElemets){
        ElemetHTML = '.'+ClassElemets;
        OutData = $(ElemetHTML);                    //Отбор элемента.
        OutData.remove();
    }
    
    
    
    //Метод выполнения удаления элемента по ID.
    UnsetHTML.DellIDElemets = function(IdElemets){
        ElemetHTML = '#'+IdElemets;
        OutData = $(ElemetHTML);                    //Отбор элемента.
        OutData.remove(); 
    }
//})//Конец главной функции.