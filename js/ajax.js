//Класс для организации AJAX запросов.



var AjaxQuery = {};                                 //Создаем класс запроса AJAX.
AjaxQuery.jPost = '';                               //Посылаемые данные на сервер.
AjaxQuery.jNamePost = '';                           //Имя посылаемых данных.
AjaxQuery.jId = '';                                 //ID объекта управления.
//AjaxQuery.jUrl = 'q_ajax.php';                      //Скрипт обработки данных.
AjaxQuery.jUrl = '';                      //Скрипт обработки данных.
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
AjaxQuery.FailureAction = function(ControlId,FailureImg){
    HtmlCode = '<img src="'+FailureImg;
    HtmlCode = HtmlCode+'">';
    ElemetHTML = '#'+ControlId;
    OutData = $(ElemetHTML);                    //Отбор элемента.
    OutData.html(HtmlCode);                     //Замена элемента.
}


//Метод выполнения укороченного действий после удачного выполнения запроса.
AjaxQuery.SuccessfulStepShort = function(HtmlCode, ControlId){
    AjaxQuery.jData = HtmlCode;                 //Сохранение данных.
    ElemetHTML = '#'+ControlId;
    OutData = $(ElemetHTML);                    //Отбор элемента.
    OutData.html(HtmlCode);                     //Замена элемента.  
}


//Метод выполнения действий после удачного выполнения запроса.
AjaxQuery.SuccessfulStep = function(HtmlCode, ControlId, NullImg){
    AjaxQuery.jData = HtmlCode;                 //Сохранение данных.
    ElemetHTML = '#'+ControlId;
    if(HtmlCode == ''){
        HtmlCode = '<img src="'+NullImg;
        HtmlCode = HtmlCode+'">';
        ElemetHTML = '#'+ControlId
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
AjaxQuery.BeforeQuery = function(ControlId,LoadImg){
    AjaxQuery.jFlagRun = false;
    HtmlCode = '<img src="'+LoadImg;
    HtmlCode = HtmlCode+'">';
    ElemetHTML = '#'+ControlId
    OutData = $(ElemetHTML);                    //Отбор элемента.
    OutData.html(HtmlCode);                     //Замена элемента.
}



//Метод обработки нажатия на кнопку и отправки асинхронного запроса.
AjaxQuery.PressButtonAsync = function(NamePost,ValuePost,ControlId,NameModule,Url,LoadImg,FailureImg,NullImg){
    AjaxQuery.jNamePost = NamePost;
    AjaxQuery.jPost = ValuePost;
    AjaxQuery.jId = ControlId;
    AjaxQuery.jNameModule = NameModule;
    AjaxQuery.jLoadImg = LoadImg;                           
    AjaxQuery.jFailureImg = FailureImg;                
    AjaxQuery.jNullImg = NullImg;
    AjaxQuery.jUrl = Url;
    AjaxQuery.AjaxPostAsync(NamePost,ValuePost,ControlId,NameModule,Url,LoadImg,FailureImg,NullImg);    
}



//Метод обработки нажатия на кнопку и отправки синхронного запроса.
AjaxQuery.PressButtonSync = function(NamePost,ValuePost,ControlId,NameModule,Url,LoadImg,FailureImg,NullImg){
    AjaxQuery.jNamePost = NamePost;
    AjaxQuery.jPost = ValuePost;
    AjaxQuery.jId = ControlId;
    AjaxQuery.jNameModule = NameModule;
    AjaxQuery.jLoadImg = LoadImg;                           
    AjaxQuery.jFailureImg = FailureImg;                
    AjaxQuery.jNullImg = NullImg;
    AjaxQuery.jUrl = Url;
    AjaxQuery.AjaxPostSync(NamePost,ValuePost,ControlId,NameModule,Url,LoadImg,FailureImg,NullImg);    
}



//Метод выполнения короткого асинхронного запроса.
AjaxQuery.AjaxPostAsyncShort = function(NamePost,ValuePost,ControlId,NameModule,Url){
    $.ajax({
        type:"POST",
        dataType: "html",
        data:({'NameModule':NameModule, 'NamePost':NamePost, 'ValuePost':ValuePost, 'AjaxControlledId':ControlId, 'ajax': true}),
        url:Url,
        success:function(data){AjaxQuery.SuccessfulStepShort(data,ControlId)}
    }) 
}




//Метод выполнения асинхронного запроса.
AjaxQuery.AjaxPostAsync = function(NamePost,ValuePost,ControlId,NameModule,Url,LoadImg,FailureImg,NullImg){
    $.ajax({
        type:"POST",
        dataType: "html",
        //async: false,   
        //contentType: 'application/x-www-form-urlencoded; charset=utf-8',
        data:({'NameModule':NameModule, 'NamePost':NamePost, 'ValuePost':ValuePost, 'AjaxControlledId':ControlId, 'ajax': true}),
        url:Url,
        error:function(){AjaxQuery.FailureAction(ControlId,FailureImg)},
        beforeSend:function(){AjaxQuery.BeforeQuery(ControlId,LoadImg)},
        complete:function(){AjaxQuery.AfterStep(ControlId,LoadImg)},
        success:function(data){AjaxQuery.SuccessfulStep(data,ControlId,NullImg)}
    }) 
}


//Метод выполнения синхронного запроса.
AjaxQuery.AjaxPostSync = function(NamePost,ValuePost,ControlId,NameModule,Url,LoadImg,FailureImg,NullImg){
    $.ajax({
        type:"POST",
        dataType: "html",
        async: false,   
        //contentType: 'application/x-www-form-urlencoded; charset=utf-8',
        data:({'NameModule':NameModule, 'NamePost':NamePost, 'ValuePost':ValuePost, 'AjaxControlledId':ControlId, 'ajax': true}),
        url:Url,
        error:function(){AjaxQuery.FailureAction(ControlId,FailureImg)},
        beforeSend:function(){AjaxQuery.BeforeQuery(ControlId,LoadImg)},
        complete:function(){AjaxQuery.AfterStep(ControlId,LoadImg)},
        success:function(data){AjaxQuery.SuccessfulStep(data,ControlId,NullImg)}
    })
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