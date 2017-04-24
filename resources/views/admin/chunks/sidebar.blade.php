<div class="sidebar content-box" style="display: block;">
    <ul class="nav">
        <!-- Main menu -->
        @if (!config('main.inputPlaylist.value'))
            <li><a href="{{url('admin/config')}}"><i class="glyphicon glyphicon-pencil"></i> Настройки</a></li>
        @else
            <li><a href="{{url('admin')}}"><i class="glyphicon glyphicon-home"></i> Главная</a></li>
            <li><a href="{{url('admin/config')}}"><i class="glyphicon glyphicon-pencil"></i> Настройки</a></li>
            <li><a href="{{route('channels')}}"><i class="glyphicon glyphicon-list"></i> Каналы</a></li>
            <li><a href="{{url('admin/add-channels')}}"><i class="glyphicon glyphicon-list"></i> Добавленные каналы</a>
            </li>
        @endif
    </ul>
</div>