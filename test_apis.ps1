# Variables
$baseUrl = "http://localhost:8000/api"
$email = "test@example.com"
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

$registerResponse = Invoke-WebRequest -Uri "$baseUrl/register" `
    -Method POST `
    -ContentType "application/json" `
    -Body $registerPayload `
    -SkipHttpErrorCheck

Write-Host "Status: $($registerResponse.StatusCode)"
$registerData = $registerResponse.Content | ConvertFrom-Json
Write-Host ($registerData | ConvertTo-Json -Depth 5)

# 2. LOGIN
Write-Host "`n2. POST /login - Iniciar sesión" -ForegroundColor Yellow
$loginPayload = @{
    email    = $email
    password = $password
} | ConvertTo-Json

$loginResponse = Invoke-WebRequest -Uri "$baseUrl/login" `
    -Method POST `
    -ContentType "application/json" `
    -Body $loginPayload `
    -SkipHttpErrorCheck

Write-Host "Status: $($loginResponse.StatusCode)"
$loginData = $loginResponse.Content | ConvertFrom-Json
$token = $loginData.token
Write-Host "Token obtenido: $token"
Write-Host ($loginData | ConvertTo-Json -Depth 5)

# 3. OBTENER USUARIO AUTENTICADO
Write-Host "`n3. GET /user - Obtener usuario autenticado" -ForegroundColor Yellow
$userResponse = Invoke-WebRequest -Uri "$baseUrl/user" `
    -Method GET `
    -Headers @{ Authorization = "Bearer $token" } `
    -SkipHttpErrorCheck

Write-Host "Status: $($userResponse.StatusCode)"
Write-Host ($userResponse.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5)

# 4. OBTENER PELÍCULAS (para saber qué IDs usar)
Write-Host "`n4. GET /peliculas - Obtener películas" -ForegroundColor Yellow
$peliculasResponse = Invoke-WebRequest -Uri "$baseUrl/peliculas" `
    -Method GET `
    -SkipHttpErrorCheck

Write-Host "Status: $($peliculasResponse.StatusCode)"
$peliculasData = $peliculasResponse.Content | ConvertFrom-Json
Write-Host "Películas disponibles:"
$peliculasData | Select-Object -First 3 | ForEach-Object { Write-Host "ID: $($_.id), Nombre: $($_.nombre)" }

# Usar la primera película disponible (ajusta si no existe)
$peliculaId = $peliculasData[0].id
Write-Host "`nUsando película ID: $peliculaId para pruebas de favoritos"

# 5. AGREGAR A FAVORITOS
Write-Host "`n5. POST /favoritos/{peliculaId} - Agregar a favoritos" -ForegroundColor Yellow
$addFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos/$peliculaId" `
    -Method POST `
    -Headers @{ Authorization = "Bearer $token" } `
    -SkipHttpErrorCheck

Write-Host "Status: $($addFavResponse.StatusCode)"
Write-Host ($addFavResponse.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5)

# 6. VERIFICAR SI ES FAVORITO
Write-Host "`n6. GET /favoritos/check/{peliculaId} - Verificar si es favorito" -ForegroundColor Yellow
$checkFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos/check/$peliculaId" `
    -Method GET `
    -Headers @{ Authorization = "Bearer $token" } `
    -SkipHttpErrorCheck

Write-Host "Status: $($checkFavResponse.StatusCode)"
Write-Host ($checkFavResponse.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5)

# 7. OBTENER FAVORITOS DEL USUARIO
Write-Host "`n7. GET /favoritos - Obtener todos los favoritos" -ForegroundColor Yellow
$getFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos" `
    -Method GET `
    -Headers @{ Authorization = "Bearer $token" } `
    -SkipHttpErrorCheck

Write-Host "Status: $($getFavResponse.StatusCode)"
Write-Host ($getFavResponse.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5)

# 8. ELIMINAR DE FAVORITOS
Write-Host "`n8. DELETE /favoritos/{peliculaId} - Eliminar de favoritos" -ForegroundColor Yellow
$delFavResponse = Invoke-WebRequest -Uri "$baseUrl/favoritos/$peliculaId" `
    -Method DELETE `
    -Headers @{ Authorization = "Bearer $token" } `
    -SkipHttpErrorCheck

Write-Host "Status: $($delFavResponse.StatusCode)"
Write-Host ($delFavResponse.Content | ConvertFrom-Json | ConvertTo-Json -Depth 5)

Write-Host "`n===== FIN DE PRUEBAS =====" -ForegroundColor Cyan
