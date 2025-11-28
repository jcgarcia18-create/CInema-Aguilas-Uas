# Variables
$baseUrl = "http://localhost:8000/api"
$timestamp = Get-Date -Format "yyyyMMddHHmmss"
$email = "test_$timestamp@example.com"
$password = "password123"
$name = "Test User"

Write-Host "===== PRUEBA DE APIs =====" -ForegroundColor Cyan

# 1. REGISTRAR USUARIO
Write-Host "`n1. POST /register - Registrar usuario" -ForegroundColor Yellow
$registerPayload = @{
    name     = $name
    email    = $email
    password = $password
} | ConvertTo-Json

try {
    $registerResponse = Invoke-WebRequest -Uri "$baseUrl/register" `
        -Method POST `
        -ContentType "application/json" `
        -Body $registerPayload `
        -UseBasicParsing

    Write-Host "Status: $($registerResponse.StatusCode)" -ForegroundColor Green
    $registerData = $registerResponse.Content | ConvertFrom-Json
    Write-Host ($registerData | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# 2. LOGIN
Write-Host "`n2. POST /login - Iniciar sesión" -ForegroundColor Yellow
$loginPayload = @{
    email    = $email
    password = $password
} | ConvertTo-Json

try {
    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/login" `
        -Method POST `
        -ContentType "application/json" `
        -Body $loginPayload `
        -UseBasicParsing

    Write-Host "Status: $($loginResponse.StatusCode)" -ForegroundColor Green
    $loginData = $loginResponse.Content | ConvertFrom-Json
    $token = $loginData.access_token
    Write-Host "Token obtenido: $token" -ForegroundColor Green
    Write-Host ($loginData | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    exit
}

# 3. OBTENER USUARIO AUTENTICADO
Write-Host "`n3. GET /user - Obtener usuario autenticado" -ForegroundColor Yellow
try {
    $userResponse = Invoke-WebRequest -Uri "$baseUrl/user" `
        -Method GET `
        -Headers @{ Authorization = "Bearer $token" } `
        -UseBasicParsing

    Write-Host "Status: $($userResponse.StatusCode)" -ForegroundColor Green
    $userData = $userResponse.Content | ConvertFrom-Json
    Write-Host ($userData | ConvertTo-Json -Depth 5)
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

# 4. OBTENER PELÍCULAS
Write-Host "`n4. GET /peliculas - Obtener películas" -ForegroundColor Yellow
try {
    $peliculasResponse = Invoke-WebRequest -Uri "$baseUrl/peliculas" `
        -Method GET `
        -UseBasicParsing

    Write-Host "Status: $($peliculasResponse.StatusCode)" -ForegroundColor Green
    $peliculasData = $peliculasResponse.Content | ConvertFrom-Json
    Write-Host "Películas disponibles (primeras 3):"
    $peliculasData | Select-Object -First 3 | ForEach-Object { Write-Host "  ID: $($_.id), Título: $($_.title)" }

    if ($peliculasData -and $peliculasData.Count -gt 0) {
        $peliculaId = $peliculasData[0].id
        Write-Host "`nUsando película ID: $peliculaId para pruebas de favoritos" -ForegroundColor Green
    } else {
        Write-Host "`nNo hay películas disponibles. Saltando pruebas de favoritos." -ForegroundColor Yellow
        $peliculaId = $null
    }
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    $peliculaId = $null
}

# 5-8. PRUEBAS DE FAVORITOS (solo si hay película)
if ($peliculaId) {

    # 5. AGREGAR A FAVORITOS
    Write-Host "`n5. POST /favoritos/{peliculaId} - Agregar a favoritos" -ForegroundColor Yellow
    try {
        $addFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos/$peliculaId" `
            -Method POST `
            -Headers @{ Authorization = "Bearer $token" } `
            -UseBasicParsing

        Write-Host "Status: $($addFavResponse.StatusCode)" -ForegroundColor Green
        $addFavData = $addFavResponse.Content | ConvertFrom-Json
        Write-Host ($addFavData | ConvertTo-Json -Depth 5)
    } catch {
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    }

    # 6. VERIFICAR SI ES FAVORITO
    Write-Host "`n6. GET /favoritos/check/{peliculaId} - Verificar si es favorito" -ForegroundColor Yellow
    try {
        $checkFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos/check/$peliculaId" `
            -Method GET `
            -Headers @{ Authorization = "Bearer $token" } `
            -UseBasicParsing

        Write-Host "Status: $($checkFavResponse.StatusCode)" -ForegroundColor Green
        $checkFavData = $checkFavResponse.Content | ConvertFrom-Json
        Write-Host ($checkFavData | ConvertTo-Json -Depth 5)
    } catch {
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    }

    # 7. OBTENER FAVORITOS DEL USUARIO
    Write-Host "`n7. GET /favoritos - Obtener todos los favoritos" -ForegroundColor Yellow
    try {
        $getFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos" `
            -Method GET `
            -Headers @{ Authorization = "Bearer $token" } `
            -UseBasicParsing

        Write-Host "Status: $($getFavResponse.StatusCode)" -ForegroundColor Green
        $getFavData = $getFavResponse.Content | ConvertFrom-Json
        Write-Host ($getFavData | ConvertTo-Json -Depth 5)
    } catch {
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    }

    # 8. ELIMINAR DE FAVORITOS
    Write-Host "`n8. DELETE /favoritos/{peliculaId} - Eliminar de favoritos" -ForegroundColor Yellow
    try {
        $delFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos/$peliculaId" `
            -Method DELETE `
            -Headers @{ Authorization = "Bearer $token" } `
            -UseBasicParsing

        Write-Host "Status: $($delFavResponse.StatusCode)" -ForegroundColor Green
        $delFavData = $delFavResponse.Content | ConvertFrom-Json
        Write-Host ($delFavData | ConvertTo-Json -Depth 5)
    } catch {
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n===== FIN DE PRUEBAS =====" -ForegroundColor Cyan
