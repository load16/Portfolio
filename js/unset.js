//����� ������������ �� ���������� ��������.
//$(document).ready(function(){
    var UnsetHTML = {};                                 //������� ����� ��� �������� ����'.
    
    
    //����� ���������� �������� HTML ������ �������� � ����� �� ��.
    UnsetHTML.UnsetId = function(IdElemets){
        ElemetHTML = '#'+IdElemets;
        OutData = $(ElemetHTML);                    //����� ��������.
        HtmlCode = '';
        OutData.html(HtmlCode); 
    }
    
    //����� ���������� �������� HTML ������ �������� � ����� ���� �� CLASS.
    UnsetHTML.UnsetClass = function(ClassElemets){
        ElemetHTML = '.'+ClassElemets;
        OutData = $(ElemetHTML);                    //����� ��������.
        HtmlCode = '';
        OutData.html(HtmlCode); 
    }
    
    
    //����� ���������� �������� �������� �� CLASS.
    UnsetHTML.DellClassElemets = function(ClassElemets){
        ElemetHTML = '.'+ClassElemets;
        OutData = $(ElemetHTML);                    //����� ��������.
        OutData.remove();
    }
    
    
    
    //����� ���������� �������� �������� �� ID.
    UnsetHTML.DellIDElemets = function(IdElemets){
        ElemetHTML = '#'+IdElemets;
        OutData = $(ElemetHTML);                    //����� ��������.
        OutData.remove(); 
    }
//})//����� ������� �������.