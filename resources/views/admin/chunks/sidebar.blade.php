<div class="sidebar content-box" style="display: block;">
    <ul class="nav">
        <!-- Main menu -->
        @if (!config('main.inputPlaylist.value'))
            <li><a href="{{url('admin/config')}}"><i class="glyphicon glyphicon-pencil"></i> Настройки</a></li>
        @else
            <li><a href="{{url('admin')}}"><i class="glyphicon glyphicon-home"></i> Главная</a></li>
            <li><a href="{{url('admin/config')}}"><i class="glyphicon glyphicon-pencil"></i> Настройки</a></li>
            <li><a href="{{url('admin/groups')}}"><i class="glyphicon glyphicon-tasks"></i> Группы каналов</a></li>
            <li><a href="{{route('channels')}}"><i class="glyphicon glyphicon-list"></i> Каналы</a></li>
            <li><a href="{{url('admin/changed-groups')}}"><i class="glyphicon glyphicon-tasks"></i> Измененные
                    группы</a></li>
            <li><a href="{{url('admin/exclude-groups')}}"><i class="glyphicon glyphicon-tasks"></i> Скрытые группы</a>
            </li>
            <li><a href="{{url('admin/changed-channels')}}"><i class="glyphicon glyphicon-list"></i> Измененные
                    каналы</a></li>
            <li><a href="{{url('admin/exclude-channels')}}"><i class="glyphicon glyphicon-list"></i> Скрытые каналы</a>
            </li>
            <li><a href="{{url('admin/add-channels')}}"><i class="glyphicon glyphicon-list"></i> Добавленные каналы</a>
            </li>
            {{--<li class="submenu">
                <a href="#">
                    <i class="glyphicon glyphicon-list"></i> Pages
                    <span class="caret pull-right"></span>
                </a>
                <!-- Sub menu -->
                <ul>
                    <li><a href="login.html">Login</a></li>
                    <li><a href="signup.html">Signup</a></li>
                </ul>
            </li>--}}
        @endif
    </ul>
</div>