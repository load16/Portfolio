//����� ��� ����������� AJAX ��������.



var AjaxQuery = {};                                 //������� ����� ������� AJAX.
AjaxQuery.jPost = '';                               //���������� ������ �� ������.
AjaxQuery.jNamePost = '';                           //��� ���������� ������.
AjaxQuery.jId = '';                                 //ID ������� ����������.
//AjaxQuery.jUrl = 'q_ajax.php';                      //������ ��������� ������.
AjaxQuery.jUrl = 'index.php';                      //������ ��������� ������.
AjaxQuery.jNameModule = '';                         //��� ������ ���������
AjaxQuery.jData = '';                               //���������� ������ � �������.
//AjaxQuery.jArrayReloadId = new Array();               //������ ������������� ���������.
//AjaxQuery.jArrayReloadFn = new Array();              

AjaxQuery.jLoadImg = '';                            //������ �� ������������ �������� ��������.
AjaxQuery.jFailureImg = '';                         //������ �� ������������ �������� ������.
AjaxQuery.jNullImg = '';                            //������ �� ������������ �������� ��������� ������.


AjaxQuery.jFlagRun = '';                            //���� ��������� ������.





$(document).ready(function(){
    

})//����� ������� �������.


//����� ���������� �������� ��� ��������� ��������� �������.
AjaxQuery.FailureAction = function(){
    HtmlCode = '<img src="'+AjaxQuery.jFailureImg;
    HtmlCode = HtmlCode+'">';
    ElemetHTML = '#'+AjaxQuery.jId;
    OutData = $(ElemetHTML);                    //����� ��������.
    OutData.html(HtmlCode);                     //������ ��������.
}


//����� ���������� �������� ����� �������� ���������� �������.
AjaxQuery.SuccessfulStep = function(HtmlCode){
    AjaxQuery.jData = HtmlCode;                 //���������� ������.
    ElemetHTML = '#'+AjaxQuery.jId;
    if(HtmlCode == ''){
        HtmlCode = '<img src="'+AjaxQuery.jNullImg;
        HtmlCode = HtmlCode+'">';
        ElemetHTML = '#'+AjaxQuery.jId
        OutData = $(ElemetHTML);                    //����� ��������.
        OutData.html(HtmlCode);                     //������ ��������.
    }
    else{
        OutData = $(ElemetHTML);                    //����� ��������.
        OutData.html(HtmlCode);                     //������ ��������.
    }   
}




//����� ���������� �������� ����� �������.
AjaxQuery.AfterStep = function(){
    AjaxQuery.jFlagRun = true;
    jArray = AjaxQuery.jArrayReload;      
}


//����� ���������� �������� ����� ��������.
AjaxQuery.BeforeQuery = function(){
    AjaxQuery.jFlagRun = false;
    HtmlCode = '<img src="'+AjaxQuery.jLoadImg;
    HtmlCode = HtmlCode+'">';
    ElemetHTML = '#'+AjaxQuery.jId
    OutData = $(ElemetHTML);                    //����� ��������.
    OutData.html(HtmlCode);                     //������ ��������.
}



//����� ��������� ������� �� ������
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



//����� ���������� �������.
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


//����� ���������� �������.
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
    //alert('������������ ���������' + NameModule); 
}



//����� ���������� �������� HTML ����.
AjaxQuery.UnsetHTML = function(IdElemets){
    ElemetHTML = '#'+IdElemets;
    OutData = $(ElemetHTML);                    //����� ��������.                  //����� ��������.
    HtmlCode = '';
    OutData.html(HtmlCode); 
}


//����� ������������ ��������.
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
    //alert('������������ ���������' + NameModule);
}

//����� �������� ���������� �������� ����������.
AjaxQuery.CreateInvisibleElement = function(){
    //document.write('<input type=\"text\" id=\"InvisibleElement\" value=\"false\"');
    //Element.appendChild('<input type="text" id="InvisibleElement" style="visibility:hidden" value="false">');
}
