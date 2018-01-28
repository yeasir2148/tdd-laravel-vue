<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Jeff\'s Forum') }}
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                &nbsp;
                @can('create','App\Thread')
                <li>
                    <a href="{{ route('thread.create') }}">Create Thread</a>
                </li>
                @endcan
                <li class="dropdown">
                    <a class=" dropdown-toggle" href="#" id="authorDropDown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">
                      Browse
                      <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="authorDropDown">
                      
                        <li><a class="dropdown-item" href="{{ route('threads.all') }}">All threads</a></li>
                        @if(auth()->check())
                        <li><a class="dropdown-item" href="{{ route('threads.all',['author'=>auth()->id()]) }}">My thread</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('threads.all',['repliesCount'=>0]) }}">Unanswered threads</a></li>
                      
                    </ul>
                </li>
                <li class="dropdown">
                    <a class=" dropdown-toggle" href="#" id="channelsDropDown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">
                      Channels
                      <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="channelsDropDown">
                      @foreach($channels as $channel)
                        <li><a class="dropdown-item" href="{{ route('channel.threads',$channel->slug) }}">{{ $channel->name }}</a></li>
                      @endforeach
                    </ul>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @else
                    <nav-notifications :passed_notifications="{{ auth()->user()->unreadNotifications }}" :curr_user="{{ auth()->user()->id }}"></nav-notifications>
                    <!-- <li class="btn-group dropdown">
                      <a class="btn btn-default btn-md top-nav-dropdown">Action</a>
                      <a class="btn btn-default btn-md top-nav-dropdown-caret dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </a>
                      <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                      </ul>
                    </li> -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="{{ route('user.profile',['user'=>Auth::user()]) }}">My Profile</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                            
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>