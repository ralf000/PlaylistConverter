<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <!-- Logo -->
                <div class="logo">
                    <h1><a href="{{url('admin')}}">{{ config('app.name', 'Laravel') }}</a></h1>
                </div>
            </div>
            <div class="col-md-2">
                <div class="user-area">
                    <div class="row">
                        <div class="col-md-6">
                            {{ Auth::user()->name }}

                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                Выйти
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>