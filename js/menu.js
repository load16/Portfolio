//����� ��� ����������� ������������ ����.


var MenuPopup = {};                                 //������� ����� ����������� ��������.

MenuPopup.ClassControlObject = '';                  //����� ������� ����������.
MenuPopup.ClassManagedObject = '';                  //����� ������������ �������.
MenuPopup.AnimationSpeedUp = 0;                    //�������� ��������.
MenuPopup.AnimationSpeedDown = 0;                  //�������� ��������.
MenuPopup.TypeAnimation = '';                       //��� ��������.
MenuPopup.StatusObject = '';

$(document).ready(function(){


})//����� ������� �������.


//����� �������� ��������� ������ ����������.
MenuPopup.MakeInvisible = function(){
    ElemetManaged = MenuPopup.ClassManagedObject;
    ManagedObj = $(ElemetManaged);
    ManagedObj.hide();
    MenuPopup.StatusObject = false;
}



//����� ����������� �������� ������ �������.
MenuPopup.MakeAnimation = function(){
    ElemetManaged = MenuPopup.ClassManagedObject;
    ManagedObj = $(ElemetManaged);
    
    ElemetControl = MenuPopup.ClassControlObject;
    ControlObj = $(ElemetControl);
    
    ControlObj.mouseup(function(){
        ElemetManaged = MenuPopup.ClassManagedObject;
        ManagedObj = $(ElemetManaged);
        if(MenuPopup.StatusObject == true){
            ManagedObj.hide(Number(MenuPopup.AnimationSpeedDown));
            MenuPopup.StatusObject = false;
        }
        else{
            ManagedObj.show(Number(MenuPopup.AnimationSpeedUp));
            MenuPopup.StatusObject = true;
        }
        
    });
    ControlObj.mouseout(function(){
        ElemetManaged = MenuPopup.ClassManagedObject;
        ManagedObj = $(ElemetManaged);
        //ManagedObj.hide(500);
        //ManagedObj.delay(2000).hide(500);
    });
    ManagedObj.mouseover(function(){
        ElemetManaged = MenuPopup.ClassManagedObject;
        ManagedObj = $(ElemetManaged);
        //ManagedObj.show();
    })
    ManagedObj.click(function(){
        ElemetManaged = MenuPopup.ClassManagedObject;
        ManagedObj = $(ElemetManaged);
        ManagedObj.hide(500);
        MenuPopup.StatusObject = false;
    })
    //document.write(ElemetControl);
}

//����� ���������������� ������� ��������.
MenuPopup.InitAnimation = function(ClassControlObject,ClassManagedObject,AnimationSpeedUp,AnimationSpeedDown,TypeAnimation){
    MenuPopup.ClassControlObject = ClassControlObject;
    MenuPopup.ClassManagedObject = ClassManagedObject;
    MenuPopup.AnimationSpeedUp = AnimationSpeedUp;
    MenuPopup.AnimationSpeedDown = AnimationSpeedDown;
    MenuPopup.TypeAnimation = TypeAnimation;
    MenuPopup.MakeInvisible();
    MenuPopup.MakeAnimation();
}