//Класс для обработки AJAX запросов.

class AjaxQuery {
	
	//Консруктор класса.
	constructor(NamePost,ValuePost,ControlId,NameModule,LoadImg,FailureImg,NullImg,jUrl){
	    this.jNamePost = NamePost;
	    this.jPost = ValuePost;
	    this.jId = ControlId;
	    this.jNameModule = NameModule;
	    this.jLoadImg = LoadImg;                           
	    this.jFailureImg = FailureImg;                
	    this.jNullImg = NullImg;
	    this.jUrl = jUrl;
	    //this.AjaxPostAsync();
	    this.test();
	}

	test(){
	    alert(this.jId);
	}
	  
	//Метод выполнения действий при неудачном выполнеии запроса.
	FailureAction(){
	    HtmlCode = '<img src="'+this.jFailureImg;
	    HtmlCode = HtmlCode+'">';
	    ElemetHTML = '#'+this.jId;
	    OutData = $(ElemetHTML);                    //Отбор элемента.
	    OutData.html(HtmlCode);                     //Замена элемента.
	}


	//Метод выполнения действий после удачного выполнения запроса.
	SuccessfulStep(HtmlCode){
	    this.jData = HtmlCode;                 //Сохранение данных.
	    ElemetHTML = '#'+this.jId;
	    if(HtmlCode == ''){
	        HtmlCode = '<img src="'+this.jNullImg;
	        HtmlCode = HtmlCode+'">';
	        ElemetHTML = '#'+this.jId
	        OutData = $(ElemetHTML);                    //Отбор элемента.
	        OutData.html(HtmlCode);                     //Замена элемента.
	    }
	    else{
	        OutData = $(ElemetHTML);                    //Отбор элемента.
	        OutData.html(HtmlCode);                     //Замена элемента.
	    }   
	}




	//Метод выполнения действий после запроса.
	AfterStep(){
	    this.jFlagRun = true;
	    jArray = this.jArrayReload;      
	}


	//Метод выполнения действий перед запросом.
	BeforeQuery(){
	    this.jFlagRun = false;
	    HtmlCode = '<img src="'+this.jLoadImg;
	    HtmlCode = HtmlCode+'">';
	    ElemetHTML = '#'+this.jId
	    OutData = $(ElemetHTML);                    //Отбор элемента.
	    OutData.html(HtmlCode);                     //Замена элемента.
	}



	//Метод обработки нажатия на кнопку
	PressButton(NamePost,ValuePost,ControlId,NameModule,LoadImg,FailureImg,NullImg){
	    this.jNamePost = NamePost;
	    this.jPost = ValuePost;
	    this.jId = ControlId;
	    this.jNameModule = NameModule;
	    this.jLoadImg = LoadImg;                           
	    this.jFailureImg = FailureImg;                
	    this.jNullImg = NullImg;
	    this.AjaxPostAsync();    
	}



	//Метод выполнения запроса.
	AjaxPostAsync(){ 
	    $.ajax({
	        type:"POST",
	        dataType: "html",
	        //async: false,   
	        //contentType: 'application/x-www-form-urlencoded; charset=windows-1251',
	        //data:({'ajax':this.jNameModule,'NamePost':this.jNamePost, 'ValuePost':this.jPost}),
	        url:this.jUrl,
	        error:function(){this.FailureAction()},
	        beforeSend:function(){this.BeforeQuery()},
	        complete:function(){this.AfterStep()},
	        success:function(data){this.SuccessfulStep(data)},
	        data:({'ajax':this.jNameModule,'NamePost':this.jNamePost, 'ValuePost':this.jPost, 'AjaxControlledId':this.jId})
	    });
	    
	}


	//Метод выполнения запроса.
	AjaxPostSync(){
	    $.ajax({
	        type:"POST",
	        dataType: "html",
	        async: false,   
	        //contentType: 'application/x-www-form-urlencoded; charset=windows-1251',
	        //data:({'ajax':this.jNameModule,'NamePost':this.jNamePost, 'ValuePost':this.jPost}),
	        url:this.jUrl,
	        error:function(){this.FailureAction()},
	        beforeSend:function(){this.BeforeQuery()},
	        complete:function(){this.AfterStep()},
	        success:function(data){this.SuccessfulStep(data)},
	        data:({'ajax':this.jNameModule,'NamePost':this.jNamePost, 'ValuePost':this.jPost, 'AjaxControlledId':this.jId})
	    })
	    //alert('Перезагрузка выполнена' + NameModule); 
	}



	//Метод выполнения удаления HTML кода.
	UnsetHTML(IdElemets){
	    ElemetHTML = '#'+IdElemets;
	    OutData = $(ElemetHTML);                    //Отбор элемента.                  //Отбор элемента.
	    HtmlCode = '';
	    OutData.html(HtmlCode); 
	}


	//Метод перезагрузка элемента.
	ReloadHTML(ValuePost,ControlId,NameModule,LoadImg,FailureImg,NullImg){
	    //this.jNamePost = NamePost;
	    this.jPost = ValuePost;
	    this.jId = ControlId;
	    this.jNameModule = NameModule;
	    this.jLoadImg = LoadImg;                           
	    this.jFailureImg = FailureImg;                
	    this.jNullImg = NullImg;
	    this.jNamePost = 'ajax_update';
	    this.AjaxPostAsync();
	    //this.AjaxPostSync;
	    //this.UnsetHTML(IdElemets);
	    //alert('Перезагрузка выполнена' + NameModule);
	} 
	  

}