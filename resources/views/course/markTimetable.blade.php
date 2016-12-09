@extends('layouts.app')

@section('js')
    <script src="/public/default/js/vue.js"></script>
    <script src="/public/default/js/common/selectPlaceholder.js"></script>
    <script src="/public/default/js/My97DatePicker/WdatePicker.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="/public/default/js/jquery-ui.min.js"></script>
    <!-- fullCalendar 2.2.5 -->
    <script src="/public/default/js/moment.min.js"></script>
    <script src="/public/default/js/fullcalendar.min.js"></script>
    <script>
        $(function () {
            /* initialize the external events
             -----------------------------------------------------------------*/
            function ini_events(ele) {
                ele.each(function () {

                    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                    // it doesn't need to have a start or end
                    var eventObject = {
                        id: $.trim($(this).attr('id')), // use the element's text as the event title
                        title: $.trim($(this).text()) // use the element's text as the event title
                    };

                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject);

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0  //  original position after the drag
                    });

                });
            }

            ini_events($('#external-events div.external-event'));

            $('#calendar').fullCalendar({
                allDaySlot : false,
                defaultView : 'agendaWeek',
                defaultEventMinutes: 120,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek'
                },
                buttonText: {
                    today: '今天',
                    week: 'week',
                },
                axisFormat:'h(:mm)t',
                dayNamesShort:['星期日', '星期一', '星期二', '星期三',
                    '星期四', '星期五', '星期六'],
                //Random default events
                events : {!! $datas !!},
                eventDrop : function(event, dayDelta, revertFunc) {
                    // save event data
                    $.ajax({
                        url : '/course/markTimetable/saveData',
                        dataType : 'json',
                        data :{
                            id : event.id,
                            title : event.title,
                            start : event.start.format('YYYY-MM-DD HH:mm:ss')
                        },
                        success : function(data) {
                            if(data.length == 0) {
                                $('#calendar').fullCalendar('refetchEvents', event);
                            }else {
                                alert(data.errorMsg);
                            }
                        }
                    });
                },
                eventClick: function(calEvent, jsEvent, view) {
                   if(confirm('确认删除！')) {
                       $.ajax({
                           url : '/course/markTimetable/deleteData',
                           dataType : 'json',
                           data :{
                               id : calEvent.id
                           },
                           success : function(data) {
                               if(data.length == 0) {
                                   $('#calendar').fullCalendar('refetchEvents', calEvent);
//                                   $('#calendar').fullCalendar('removeEvents', calEvent.id);
                               }else {
                                   alert(data.errorMsg);
                               }
                           }
                       });
                   }
                },
                editable : true,
                droppable : true, // this allows things to be dropped onto the calendar !!!
                drop: function (date, allDay) { // this function is called when something is dropped

                    // retrieve the dropped element's stored Event Object
                    var originalEventObject = $(this).data('eventObject');

                    // we need to copy it, so that multiple events don't have a reference to the same object
                    var copiedEventObject = $.extend({}, originalEventObject);

                    // assign it the date that was reported
                    copiedEventObject.start = date;
                    copiedEventObject.backgroundColor = $(this).css("background-color");
                    copiedEventObject.borderColor = $(this).css("border-color");

                    // render the event on the calendar
                    // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                    $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

                    // is the "remove after drop" checkbox checked?
                    if ($('#drop-remove').is(':checked')) {
                        // if so, remove the element from the "Draggable Events" list
                        $(this).remove();
                    }

                }
            });
        });
    </script>
@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3"    >
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">Draggable Events</h4>
                    </div>
                    <div class="box-body">
                        <!-- the events -->
                        <div id="external-events">
                            @if(!empty($courseNames))
                                @foreach($courseNames as $name => $courseInfo)
                                    <div class="external-event bg-green" id="{{$courseInfo['id']}}">{{$name}} ({{$courseInfo['teacher']}})</div>
                                @endforeach
                            @endif
                            <div class="checkbox">
                                <label for="drop-remove">
                                    <input type="checkbox" id="drop-remove">
                                    remove after drop
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body no-padding">
                        <!-- THE CALENDAR -->
                        <div id="calendar"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection