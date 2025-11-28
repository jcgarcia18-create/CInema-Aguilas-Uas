<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinemas Aguilas Uas - Home</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @php $urls = $peliculas->pluck('video_url', 'id'); @endphp
    <script>
        window.dashboardPlaybackLogRoute = "{{ route('playback.log') }}";
    window.peliculaUrls = JSON.parse('{!! addslashes(json_encode($urls)) !!}');
    </script>
    <script src="{{ asset('js/dashboard.js') }}" defer></script>
</head>
<body>

    <header class="header">
    
        <div class="logo">Cinemas<span>AguilasUas</span></div>
        <nav>
            <ul class="menu">
                <li><a href="#seguir-viendo">Seguir Viendo</a></li>
                <li><a href="#accion">Acción</a></li>
                <li><a href="#terror">Terror</a></li>
                <li><a href="#comedia">Comedia</a></li>
                <li><a href="#drama">Drama</a></li>
                <li><a href="#ciencia-ficcion">Ciencia Ficción</a></li>
            </ul>
        </nav>
        <div class="user-options">
            <a href="#" class="btn">Mi perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>



    <main class="content">
        <!-- Secciones dinámicas por género -->
        @php
            // Obtener todos los géneros únicos, aunque estén en campos con múltiples géneros separados por coma
            $allGenres = collect();
            foreach ($peliculas as $pelicula) {
                $genres = array_map('trim', explode(',', $pelicula->genre));
                foreach ($genres as $g) {
                    $allGenres->push($g);
                }
            }
            $uniqueGenres = $allGenres->unique()->sort();
        @endphp

    
        @if ($peliculasSeguirViendo->count() > 0)
            <section class="movie-section" id="seguir-viendo">
                <h2 class="section-title">Seguir Viendo</h2>
                <div class="movie-carousel">
                    <div class="movie-scroll">
                        
                        @foreach ($peliculasSeguirViendo as $pelicula)
                            <div class="movie-card">
                                <a href="#" onclick="openMovieTab('{{ json_encode($pelicula) }}')">
                                    <img src="{{ $pelicula->poster_url }}" alt="{{ $pelicula->title }}">
                                </a>
                                <div class="movie-info">
                                    <h3>{{ $pelicula->title }}</h3>
                                    <p style="font-size:0.9rem; margin-bottom:0.5rem;">{{ $pelicula->genre }} · {{ $pelicula->duration_minutes }} min</p>
                                    <div class="movie-actions">
                                          <a href="javascript:void(0)" 
                                                                  class="btn-play" 
                                                                  onclick="logView('{{ $pelicula->id }}', '{{ $pelicula->video_url }}')">
                                                                  ▶ Reproducir
                                                              </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                    </div>
                </div>
            </section>
        @endif

            @foreach ($uniqueGenres as $genero)
            <section class="movie-section" id="{{ Str::slug($genero) }}">
                <h2 class="section-title">{{ $genero }}</h2>
                <div class="movie-carousel">
                    <div class="movie-scroll">
                        @foreach ($peliculas as $pelicula)
                            @php
                                $genres = array_map('trim', explode(',', $pelicula->genre));
                            @endphp
                            @if (in_array($genero, $genres))
                                <div class="movie-card">
                                    <a href="#" onclick="openMovieTab('{{ json_encode($pelicula) }}')">
                                        <img src="{{ $pelicula->poster_url }}" alt="{{ $pelicula->title }}">
                                    </a>
                                    <div class="movie-info">
                                        <h3>{{ $pelicula->title }}</h3>
                                        <p style="font-size:0.9rem; margin-bottom:0.5rem;">{{ $pelicula->genre }} · {{ $pelicula->duration_minutes }} min</p>
                                        <div class="movie-actions">
                                                <a href="javascript:void(0)" 
                                                                 class="btn-play" 
                                                                 onclick="logView('{{ $pelicula->id }}', '{{ $pelicula->video_url }}')">
                                                                 ▶ Reproducir
                                                             </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
           @endforeach
    </main>

    <footer>
        <p>© 2025 Cinemas Aguilas Uas | Universidad Autónoma de Sinaloa</p>
    </footer>


</body>
</html>
