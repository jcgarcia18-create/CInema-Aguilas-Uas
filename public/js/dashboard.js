// JS de dashboard

function openMovieTab(peliculaJson) {
    const pelicula = JSON.parse(peliculaJson);
    const win = window.open('', '_blank');
    win.document.write(`
        <html lang="es">
        <head>
            <title>${pelicula.title}</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: 'Poppins', sans-serif; background: #001F3F; color: #fff; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: #012040; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); padding: 2rem; }
                img { width: 100%; border-radius: 8px; margin-bottom: 1.5rem; }
                h1 { color: #FFD700; margin-bottom: 1rem; }
                .meta { color: #FFD700; margin-bottom: 1rem; }
                .desc { margin-bottom: 2rem; }
                .btn-play { background: #FFD700; color: #001F3F; padding: 0.7rem 1.5rem; border-radius: 8px; font-weight: bold; text-decoration: none; font-size: 1.1rem; display: inline-block; }
                .btn-play:hover { background: #fff; color: #001F3F; }
                .btn-back { background: #4b5563; color: #fff; padding: 0.7rem 1.5rem; border-radius: 8px; font-weight: bold; text-decoration: none; font-size: 1.1rem; display: inline-block; margin-top: 1.5rem; border: none; cursor: pointer; }
                .btn-back:hover { background: #FFD700; color: #001F3F; }
            </style>
            <script>
                function clickReproducir() {
                    // Registrar la vista y redirigir a la URL del video
                    if (window.opener && window.opener.logView) {
                        window.opener.logView(${pelicula.id}, "${pelicula.video_url}"); 
                    }
                    // Abrir el video en una nueva pestaña si es una URL válida
                    if ('${pelicula.video_url}'.startsWith('http')) {
                        window.open('${pelicula.video_url}', '_blank');
                    } else {
                        window.location.href = '${pelicula.video_url}';
                    }
                }
            <\/script>
        </head>
        <body>
            <div class="container">
                <img src="${pelicula.poster_url}" alt="${pelicula.title}">
                <h1>${pelicula.title}</h1>
                <div class="meta">${pelicula.genre} · ${pelicula.duration_minutes} min</div>
                <div class="desc">${pelicula.description}</div>
                <a href="javascript:void(0)" class="btn-play" onclick="clickReproducir()">▶ Reproducir</a>
                <button class="btn-back" onclick="window.close()">Volver al dashboard</button>
            </div>
        </body>
        </html>+
    `);
    win.document.close();
}

function logView(peliculaId, videoUrl) {
    // Asegura que peliculaId sea un número
    const idNum = typeof peliculaId === 'string' ? parseInt(peliculaId, 10) : peliculaId;
    // Validar la URL antes de abrir
    if (videoUrl && videoUrl.startsWith('http')) {
        window.open(videoUrl, '_blank');
    } else {
        console.error('URL de video no válida:', videoUrl);
    }
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
        console.error('¡Error de CSRF! No se encontró la meta-etiqueta.');
        return false;
    }
    const data = { pelicula_id: idNum };
    fetch(window.dashboardPlaybackLogRoute, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify(data),
        keepalive: true 
    })
    .then(response => {
        if (!response.ok) {
            console.error('Error al guardar historial: ' + response.status);
            throw new Error('Fallo del servidor: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Historial guardado para pelicula ID:', idNum);
        } else {
            console.error('Error de lógica al guardar historial:', data.error);
        }
    })
    .catch(error => {
        console.error('Error de red al guardar historial:', error);
    });
    return false;
}

// Scroll suave al hacer clic en el menú

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 90,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    document.querySelectorAll('.movie-scroll').forEach(carousel => {
        let isDown = false;
        let startX;
        let scrollLeft;
        carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            carousel.classList.add('active');
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });
        carousel.addEventListener('mouseleave', () => {
            isDown = false;
            carousel.classList.remove('active');
        });
        carousel.addEventListener('mouseup', () => {
            isDown = false;
            carousel.classList.remove('active');
        });
        carousel.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2;
            carousel.scrollLeft = scrollLeft - walk;
        });
    });
    document.querySelectorAll('.movie-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.boxShadow = '0 20px 40px rgba(255, 215, 0, 0.3)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.boxShadow = 'none';
        });
    });
});
