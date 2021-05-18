//Класс для организации AJAX запросов.



var AjaxQuery = {};                                 //Создаем класс запроса AJAX.
AjaxQuery.jPost = '';                               //Посылаемые данные на сервер.
AjaxQuery.jNamePost = '';                           //Имя посылаемых данных.
AjaxQuery.jId = '';                                 //ID объекта управления.
//AjaxQuery.jUrl = 'q_ajax.php';                      //Скрипт обработки данных.
AjaxQuery.jUrl = 'index.php';                      //Скрипт обработки данных.
AjaxQuery.jNameModule = '';                         //Имя модуля обработки
AjaxQuery.jData = '';                               //Полученные данные с сервера.
//AjaxQuery.jArrayReloadId = new Array();               //Массив перегружаемых елементов.
//AjaxQuery.jArrayReloadFn = new Array();              

AjaxQuery.jLoadImg = '';                            //Ссылка на анимационную картинку загрухки.
AjaxQuery.jFailureImg = '';                         //Ссылка на анимационную картинку ошибки.
AjaxQuery.jNullImg = '';                            //Ссылка на анимационную картинку отсуствия данных.


AjaxQuery.jFlagRun = '';                            //Флаг выполнеия запоса.





$(document).ready(function(){
    

})//Конец главной функции.


//Метод выполнения действий при неудачном выполнеии запроса.
AjaxQuery.FailureAction = function(){
    HtmlCode = '<img src="'+AjaxQuery.jFailureImg;
    HtmlCode = HtmlCode+'">';
    ElemetHTML = '#'+AjaxQuery.jId;
    OutData = $(ElemetHTML);                    //Отбор элемента.
    OutData.html(HtmlCode);                     //Замена элемента.
}


//Метод выполнения действий после удачного выполнения запроса.
AjaxQuery.SuccessfulStep = function(HtmlCode){
    AjaxQuery.jData = HtmlCode;                 //Сохранение данных.
    ElemetHTML = '#'+AjaxQuery.jId;
    if(HtmlCode == ''){
        HtmlCode = '<img src="'+AjaxQuery.jNullImg;
        HtmlCode = HtmlCode+'">';
        ElemetHTML = '#'+AjaxQuery.jId
        OutData = $(ElemetHTML);                    //Отбор элемента.
        OutData.html(HtmlCode);                     //Замена элемента.
    }
    else{
        OutData = $(ElemetHTML);                    //Отбор элемента.
        OutData.html(HtmlCode);                     //Замена элемента.
    }   
}




//Метод выполнения действий после запроса.
AjaxQuery.AfterStep = function(){
    AjaxQuery.jFlagRun = true;
    jArray = AjaxQuery.jArrayReload;      
}


//Метод выполнения действий перед запросом.
AjaxQuery.BeforeQuery = function(){
    AjaxQuery.jFlagRun = false;
    HtmlCode = '<img src="'+AjaxQuery.jLoadImg;
    HtmlCode = HtmlCode+'">';
    ElemetHTML = '#'+AjaxQuery.jId
    OutData = $(ElemetHTML);                    //Отбор элемента.
    OutData.html(HtmlCode);                     //Замена элемента.
}



//Метод обработки нажатия на кнопку
AjaxQuery.PressButton = function(NamePost,ValuePost,ControlId,NameModule,LoadImg,FailureImg,NullImg){
    AjaxQuery.jNamePost = NamePost;
    AjaxQuery.jPost = ValuePost;
    AjaxQuery.jId = ControlId;
    AjaxQuery.jNameModule = NameModule;
    AjaxQuery.jLoadImg = LoadImg;                           
    AjaxQuery.jFailureImg = FailureImg;                
    AjaxQuery.jNullImg = NullImg;
    AjaxQuery.AjaxPostAsync();    
}



//Метод выполнения запроса.
AjaxQuery.AjaxPostAsync = function(){
    $.ajax({
        type:"POST",
        dataType: "html",
        //async: false,   
        //contentType: 'application/x-www-form-urlencoded; charset=windows-1251',
        //data:({'ajax':AjaxQuery.jNameModule,'NamePost':AjaxQuery.jNamePost, 'ValuePost':AjaxQuery.jPost}),
        url:AjaxQuery.jUrl,
        error:function(){AjaxQuery.FailureAction()},
        beforeSend:function(){AjaxQuery.BeforeQuery()},
        complete:function(){AjaxQuery.AfterStep()},
        success:function(data){AjaxQuery.SuccessfulStep(data)},
        data:({'ajax':AjaxQuery.jNameModule,'NamePost':AjaxQuery.jNamePost, 'ValuePost':AjaxQuery.jPost, 'AjaxControlledId':AjaxQuery.jId})
    }) 
}


//Метод выполнения запроса.
AjaxQuery.AjaxPostSync = function(){
    $.ajax({
        type:"POST",
        dataType: "html",
        async: false,   
        //contentType: 'application/x-www-form-urlencoded; charset=windows-1251',
        //data:({'ajax':AjaxQuery.jNameModule,'NamePost':AjaxQuery.jNamePost, 'ValuePost':AjaxQuery.jPost}),
        url:AjaxQuery.jUrl,
        error:function(){AjaxQuery.FailureAction()},
        beforeSend:function(){AjaxQuery.BeforeQuery()},
        complete:function(){AjaxQuery.AfterStep()},
        success:function(data){AjaxQuery.SuccessfulStep(data)},
        data:({'ajax':AjaxQuery.jNameModule,'NamePost':AjaxQuery.jNamePost, 'ValuePost':AjaxQuery.jPost, 'AjaxControlledId':AjaxQuery.jId})
    })
    //alert('Перезагрузка выполнена' + NameModule); 
}



//Метод выполнения удаления HTML кода.
AjaxQuery.UnsetHTML = function(IdElemets){
    ElemetHTML = '#'+IdElemets;
    OutData = $(ElemetHTML);                    //Отбор элемента.                  //Отбор элемента.
    HtmlCode = '';
    OutData.html(HtmlCode); 
}


//Метод перезагрузка элемента.
AjaxQuery.ReloadHTML = function(ValuePost,ControlId,NameModule,LoadImg,FailureImg,NullImg){
    //AjaxQuery.jNamePost = NamePost;
    AjaxQuery.jPost = ValuePost;
    AjaxQuery.jId = ControlId;
    AjaxQuery.jNameModule = NameModule;
    AjaxQuery.jLoadImg = LoadImg;                           
    AjaxQuery.jFailureImg = FailureImg;                
    AjaxQuery.jNullImg = NullImg;
    AjaxQuery.jNamePost = 'ajax_update';
    AjaxQuery.AjaxPostAsync();
    //AjaxQuery.AjaxPostSync;
    //AjaxQuery.UnsetHTML(IdElemets);
    //alert('Перезагрузка выполнена' + NameModule);
}

//Метод создания невидимого элемента управления.
AjaxQuery.CreateInvisibleElement = function(){
    //document.write('<input type=\"text\" id=\"InvisibleElement\" value=\"false\"');
    //Element.appendChild('<input type="text" id="InvisibleElement" style="visibility:hidden" value="false">');
}
