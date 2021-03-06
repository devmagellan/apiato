@extends('adminpanel::layouts.app_admin')
@section('styles')
    <link rel="stylesheet" media="screen, print" href="/templates/smartadmin/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
@endsection
@section('theme_scripts')


    <?
    $createdAt = \Carbon\Carbon::now();
    $today=$createdAt->format('m/d/Y');

    ?>


@endsection
@section('content')


    <?
    $user=\Auth::user();
    if($user->hasRole('Gods_mode')){
        $company_temp=true;
    }
    else{
        $company_temp=false;
    }
    ?>
    <main id="js-page-content" role="main" class="page-content">
        <ol class="breadcrumb page-breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">SmartAdmin</a></li>
            <li class="breadcrumb-item">Application Intel</li>
            <li class="breadcrumb-item active">Marketing Dashboard</li>
            <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
        </ol>
        <div class="subheader">
            <h1 class="subheader-title">
                <i class='subheader-icon fal fa-chart-area'></i> Marketing <span class='fw-300'>Dashboard</span>
            </h1>
            <div class="d-flex mr-4">
                <div class="mr-2">
                    <span class="peity-donut" data-peity="{ &quot;fill&quot;: [&quot;#967bbd&quot;, &quot;#ccbfdf&quot;],  &quot;innerRadius&quot;: 14, &quot;radius&quot;: 20 }">7/10</span>
                </div>
                <div>
                    <label class="fs-sm mb-0 mt-2 mt-md-0">New Sessions</label>
                    <h4 class="font-weight-bold mb-0">70.60%</h4>
                </div>
            </div>
            <div class="d-flex mr-0">
                <div class="mr-2">
                    <span class="peity-donut" data-peity="{ &quot;fill&quot;: [&quot;#2196F3&quot;, &quot;#9acffa&quot;],  &quot;innerRadius&quot;: 14, &quot;radius&quot;: 20 }">3/10</span>
                </div>
                <div>
                    <label class="fs-sm mb-0 mt-2 mt-md-0">Page Views</label>
                    <h4 class="font-weight-bold mb-0">14,134</h4>
                </div>
            </div>
        </div>

        <div class="demo">

            <button type="button" onclick="clearCustomerAdding()" class="btn btn-lg btn-primary waves-effect waves-themed" data-toggle="modal" data-target=".default-example-modal-right-lg-user">
                <span class="fal fa-plus  mr-1"></span>
                Создать пользователя</button>
        </div>


        <div id="panel-7" class="panel">
            <div class="panel-hdr">
                <h2>
                    Таблица  <span class="fw-300"><i>всех пользователей компании</i></span>
                </h2>
                <div class="panel-toolbar">
               </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="panel-tag">
                        Вы можете редактировать информацию нажав на  <a href="utilities_color_pallet.html" title="Color Pallets">карандаш</a> справа от информации
                    </div>
                    <h5 class="frame-heading">
                        Пользователи
                    </h5>
                    <div id="loader">
                        <div class="border p-3">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-tag result_of_customers_table">


                    </div>

                </div>
            </div>
        </div>


    <div class="modal fade default-example-modal-right-lg-user" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-right modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4">Форма добавления пользователя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>

                <form class="needs-validation" id="customer_create" novalidate onsubmit="theSubmitFunction(); return false;">

                <div class="modal-body">
                    <input type="hidden" id="customer_id" name="customer_id" value="0">
                    <input type="hidden" id="company_id" name="company_id" value="1">
                    <input type="hidden" id="manager_id" name="manager_id" value="1">




                    <div class="form-group">
                        <label class="form-label" for="customer_name">Имя пользователя</label>
                        <input type="text" id="customer_name" name="customer_name" required class="form-control" placeholder="Имя пользователя">

                    </div>
                    <div class="form-group">
                        <label class="form-label" for="customer_sername">Фамилия пользователя</label>
                        <input type="text" id="customer_sername" name="customer_sername" required class="form-control" placeholder="Фамилия пользователя">

                    </div>

                    <div class="form-group">
                        <label class="form-label" for="customer_sex">Пол</label>
                        <select class="form-control" id="customer_sex">
                            <option value="1">Male</option>
                            <option value="0">Female</option>
                        </select>
                    </div>

                    @if($company_temp)
                        <div class="form-group">
                            <label class="form-label" for="select">Выбрать этому пользователю компанию</label>
                            <select   class="form-control" id="selectCompany">
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->name}} </option>
                                @endforeach
                            </select>




                        </div>
                    @endif
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="managerSwitch" >
                            <label class="custom-control-label" for="managerSwitch">Менеджер/не менеджер</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="select">Назначить этому пользователю менеджера</label>
                        <select   class="form-control" id="select">
                             {{-- @foreach($managers as $manager)
                                    <option value="{{$manager->id}}">{{$manager->user->name}} {{$manager->user->sername}}</option>
                                @endforeach--}}
                        </select>




                    </div>

                    <div class="form-group">
                        <label class="form-label" for="customer_location">Местонахождение пользователя</label>
                        <input type="text" id="customer_location" name="customer_location" class="form-control" required placeholder="Локация пользователя">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="customer_email">Email</label>
                        <input type="email" id="customer_email" name="customer_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required email class="form-control" placeholder="Email">
                        <span class="has_been_taken_message" style="display:none;color:red"> Email has been taken</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="company_department">Департамент</label>
                        <input type="text" id="customer_department" name="customer_department" required class="form-control" placeholder="Департамент">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="company_position">Должность</label>
                        <input type="text" id="customer_position" name="customer_position" required class="form-control" placeholder="Должность">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="customer_phone">Контактный телефон</label>
                        <input type="text" id="customer_phone" name="customer_phone" required class="form-control" placeholder="Контактный телефон">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label col-12 col-lg-3 form-label text-lg-right">Birth date</label>
                        <div class="col-12 col-lg-6 ">
                            <input type="text" class="form-control" id="datepicker-1" readonly placeholder="Select date" value="{{$today}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label col-12 col-lg-3 form-label text-lg-right">Start date</label>
                        <div class="col-12 col-lg-6 ">
                            <input type="text" class="form-control" id="datepicker-2" readonly placeholder="Select date" value="{{$today}}">
                        </div>
                    </div>






                </div>
                <div class="modal-footer">
                    <button type="button" class="customer_create_close btn btn-secondary waves-effect waves-themed" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="customer_create btn btn-primary waves-effect waves-themed" >Сохранить</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    </main>
@endsection

@section('scripts')
    <script src="/templates/smartadmin/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script>


        // Class definition

        var controls = {
            leftArrow: '<i class="fal fa-angle-left" style="font-size: 1.25rem"></i>',
            rightArrow: '<i class="fal fa-angle-right" style="font-size: 1.25rem"></i>'
        }

        var runDatePicker = function()
        {

            // minimum setup
            $('#datepicker-1').datepicker(
                {
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: controls
                });


            // input group layouts
            $('#datepicker-2').datepicker(
                {
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: controls
                });

            // input group layouts for modal demo
            $('#datepicker-modal-2').datepicker(
                {
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: controls
                });

            // enable clear button
            $('#datepicker-3').datepicker(
                {
                    todayBtn: "linked",
                    clearBtn: true,
                    todayHighlight: true,
                    templates: controls
                });

            // enable clear button for modal demo
            $('#datepicker-modal-3').datepicker(
                {
                    todayBtn: "linked",
                    clearBtn: true,
                    todayHighlight: true,
                    templates: controls
                });

            // orientation
            $('#datepicker-4-1').datepicker(
                {
                    orientation: "top left",
                    todayHighlight: true,
                    templates: controls
                });

            $('#datepicker-4-2').datepicker(
                {
                    orientation: "top right",
                    todayHighlight: true,
                    templates: controls
                });

            $('#datepicker-4-3').datepicker(
                {
                    orientation: "bottom left",
                    todayHighlight: true,
                    templates: controls
                });

            $('#datepicker-4-4').datepicker(
                {
                    orientation: "bottom right",
                    todayHighlight: true,
                    templates: controls
                });

            // range picker
            $('#datepicker-5').datepicker(
                {
                    todayHighlight: true,
                    templates: controls
                });

            // inline picker
            $('#datepicker-6').datepicker(
                {
                    todayHighlight: true,
                    templates: controls
                });
        }


$(document).ready(function(){ runDatePicker();})

$('#managerSwitch').change(function(){
    console.log('Manager1',$(this).is(':checked'))
})



        $('#customer_email').change(function(){
            var company_email = $('#customer_email').val()

            $.ajax({
                url: '/user/email_check',
                method: 'POST',
                dataType: 'json',
                async: false,
                data: {
                    'email_check': 1,
                    'email': company_email,
                },
                success: function (response) {
                    console.log(response )
                    if (response == 'taken') {
                        localStorage.setItem('email_state',1);
                        console.log(response )
                        $('#customer_email').addClass("is-invalid");
                        $('#customer_email').removeClass("is-valid");
                        /*           $('#company_email').parent().removeClass();
                         $('#company_email').parent().addClass("form_error");
                         $('#company_email').siblings("span").text('Sorry... Email already taken');*/
                    } else if (response == 'not_taken') {
                        console.log(response )
                        localStorage.setItem('email_state',0);
                        $('#customer_email').removeClass("is-invalid");
                        $('#customer_email').addClass("is-valid");
                        /*       $('#company_email').parent().removeClass();
                         $('#company_email').parent().addClass("form_success");
                         $('#company_email').siblings("span").text('Email available');*/
                    }
                }
            });
        })



       /* $('.customer_create').click(function(){*/
function  theSubmitFunction () {

$('.has_been_taken_message').hide();
    var form=$('#customer_create')
    if (form[0].checkValidity() === false || localStorage.getItem('email_state') == 1) {
    if(localStorage.getItem('email_state') == 1){
        console.log(555)
        $('#customer_email').closest('.form-control').removeClass('is-valid').addClass('is-invalid')
        $('#customer_create').removeClass('was-validated')
        $('.has_been_taken_message').show();
    }
console.log(777)
    }
    else {

        $('#customer_create').addClass('was-validated')
        console.log(222, company_id)
        @if($company_temp)
           company_id=$('#selectCompany').val()
        @endif
        var customer_name = $('#customer_name').val()
        var customer_sername = $('#customer_sername').val()
        var customer_sex = $('#customer_sex').val()
        var customer_location = $('#customer_location').val()
        if($('#customer_id').val()){
            var customer_id = $('#customer_id').val()
        }
        else{
            var customer_id =0;
        }

        var manager = $('#managerSwitch').is(':checked')
        var manager_id = $('#select').val()
        var customer_department = $('#customer_department').val()
        var customer_position = $('#customer_position').val()

        console.log('customer_position',customer_position)
        console.log('customer_id',customer_id)
        var customer_email = $('#customer_email').val()
        var customer_birth_date = $('#datepicker-1').val()
        var customer_start_date = $('#datepicker-2').val()
        console.log('customer_id=', customer_id, 'customer_name=>', customer_name, 'customer_sername=>', customer_sername,
            'customer_sex=>', customer_sex, 'customer_location=>', customer_location,
            'customer_department=>', customer_department, 'company_id=>', company_id, 'manager_id=>', manager_id,
            'customer_position=>' ,customer_position,
            'customer_email=>', customer_email, 'manager=>', manager,
            'birth_date=>',customer_birth_date,
            'start_date=>',customer_start_date);

        $.ajax({
            method: 'POST',
            dataType: 'json',
            async: false,
            url: '/users/create',
            data: {
                customer_id: customer_id, customer_name: customer_name, sername: customer_sername,
                sex: customer_sex, location: customer_location,manager_id: manager_id,
                position: customer_position,
                email: customer_email, manager: manager,
                birth_date:customer_birth_date,
                start_date:customer_start_date
            },
            beforeSend: function () {
            },
            complete: function () {
                $('.customer_create_close').click();
                $('#customer_id').val('')
                //reloadData();

            },
            success: function (data) {

                console.log('success')
                //reloadData();
            }
        });
    }
}
      /*  })*/



        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        })

        reloadData();
        function reloadData(){

            var module='admin.company.users.data'
            var url='/users/data';
            $.ajax({
                method: 'POST',
                dataType: 'html',
                async:true,
                url: url,
                data: {module: module},
                beforeSend: function() {
                    $('#loader').show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                success: function (data) {

                    $('.result_of_customers_table').html(data);


                }
            });


        }


(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                if(localStorage.getItem('email_state') != 1){
                    console.log(888)
                form.classList.add('was-validated');}
            }, false);
        });
    }, false);
})();


function clearCustomerAdding(){
    $('#customer_create').removeClass('was-validated');
    $('#customer_id').val("")
    $('#customer_name').val("")
    $('#customer_sername').val("")
    $('#customer_location').val("")
    $('#customer_department').val("")
    $('#customer_position').val("")
    $('#customer_email').val("")
    $('#customer_phone').val("")

}


    </script>
@endsection
