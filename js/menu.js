//Класс для организации всплывающего меню.


var MenuPopup = {};                                 //Создаем класс всплывающих объектов.

MenuPopup.ClassControlObject = '';                  //Класс объекта управления.
MenuPopup.ClassManagedObject = '';                  //Класс управляемого объекта.
MenuPopup.AnimationSpeedUp = 0;                    //Скорость анимации.
MenuPopup.AnimationSpeedDown = 0;                  //Скорость анимации.
MenuPopup.TypeAnimation = '';                       //Тип анимации.
MenuPopup.StatusObject = '';

$(document).ready(function(){


})//Конец главной функции.


//Метод делающий невидимым объект управления.
MenuPopup.MakeInvisible = function(){
    ElemetManaged = MenuPopup.ClassManagedObject;
    ManagedObj = $(ElemetManaged);
    ManagedObj.hide();
    MenuPopup.StatusObject = false;
}



//Метод выполняющий анимацию вывода объекта.
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

//Метод инициализирующий процесс инимации.
MenuPopup.InitAnimation = function(ClassControlObject,ClassManagedObject,AnimationSpeedUp,AnimationSpeedDown,TypeAnimation){
    MenuPopup.ClassControlObject = ClassControlObject;
    MenuPopup.ClassManagedObject = ClassManagedObject;
    MenuPopup.AnimationSpeedUp = AnimationSpeedUp;
    MenuPopup.AnimationSpeedDown = AnimationSpeedDown;
    MenuPopup.TypeAnimation = TypeAnimation;
    MenuPopup.MakeInvisible();
    MenuPopup.MakeAnimation();
}