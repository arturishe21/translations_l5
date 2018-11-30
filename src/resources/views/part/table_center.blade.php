<script>
     $(".breadcrumb").html("<li><a href='/admin'>{{__cms('Главная')}}</a></li> <li>{{__cms('Переводы')}}</li>");
     $("title").text("{{__cms('Переводы')}} - {{ __cms(config('builder.admin.caption')) }}");
   </script>

<!-- MAIN CONTENT -->
         <div class="jarviswidget jarviswidget-color-blue " id="wid-id-4" data-widget-editbutton="false" data-widget-colorbutton="false">
                        <header>
                            <span class="widget-icon"> <i class="fa  fa-file-text"></i> </span>
                            <h2> {{__cms('Переводы')}} </h2>
                        </header>
                         <div class="table_center no-padding">

                  <div class="dt-toolbar">
                      <div class="col-xs-12 col-sm-6">
                          <div id="dt_basic_filter" class="dataTables_filter">
                           <form action="" method="get" id="search_form">
                              <label>
                                  <span class="input-group-addon">
                                  <i class="glyphicon glyphicon-search"></i>
                                  </span>
                                  <input class="form-control" name="search_q" type="search" value="{{$search_q ?? ""}}" aria-controls="dt_basic">
                              </label>
                             </form>
                          </div>
                      </div>
                </div>

                <div class="result_table">
                     @include("translations::part.result_search")
                </div>

            </div>
      </div>

    <!-- END MAIN CONTENT -->
<div id="modal_wrapper">
   @include("translations::part.pop_trans_add")
</div>
<div class='load_ajax'></div>
<script src="{{asset('packages/vis/translations/js/translations.js')}}"></script>
