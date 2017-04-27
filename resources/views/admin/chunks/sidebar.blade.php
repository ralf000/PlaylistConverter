<div class="sidebar content-box" style="display: block;">
    <ul class="nav">
        <!-- Main menu -->
        <li><a href="{{url('admin')}}"><i class="glyphicon glyphicon-home"></i> Главная</a></li>
        <li><a href="{{url('admin/config')}}"><i class="glyphicon glyphicon-pencil"></i> Настройки</a></li>
        <li><a href="{{route('channels')}}"><i class="glyphicon glyphicon-list"></i> Каналы</a></li>
{{--        @if (!Config::get('builderMode'))
            <li><a href="{{url('admin/own-channels')}}"><i class="glyphicon glyphicon-list"></i> Собственные каналы</a>
            </li>
        @endif--}}
    </ul>
</div>