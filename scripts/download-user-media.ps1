param([int]$GalleryPerUser = 3)
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$mediaRoot = Join-Path $root 'assets/media/users'
$folders = @('profiles','covers','gallery','intro-videos')
foreach ($folder in $folders) { New-Item -ItemType Directory -Force -Path (Join-Path $mediaRoot $folder) | Out-Null }
$cacheRoot = Join-Path $mediaRoot '.source-cache'; New-Item -ItemType Directory -Force -Path $cacheRoot | Out-Null

Add-Type -AssemblyName System.Drawing
function Search-Commons([string]$query, [int]$limit, [string]$kind = 'bitmap') {
    $encoded = [uri]::EscapeDataString("$query filetype:$kind")
    $url = "https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrsearch=$encoded&gsrnamespace=6&gsrlimit=$limit&prop=imageinfo&iiprop=url%7Csize%7Cmime%7Cextmetadata&iiurlwidth=1600&format=json"
    $response = Invoke-RestMethod -Uri $url -Headers @{ 'User-Agent'='SornazSeedMedia/1.0 (development fixtures)' }
    return @($response.query.pages.PSObject.Properties.Value | Sort-Object index)
}
function Save-CroppedImage($page, [string]$path, [int]$width, [int]$height) {
    if (Test-Path $path) { return }
    $info = $page.imageinfo[0]
    $source = if ($info.thumburl) { $info.thumburl } else { $info.url }
    $cacheName = ([string]$page.pageid) + '.source'; $temp = Join-Path $cacheRoot $cacheName
    try {
        if (!(Test-Path $temp)) {
            for ($attempt=1; $attempt -le 5; $attempt++) {
                try { Invoke-WebRequest -Uri $source -OutFile $temp -Headers @{ 'User-Agent'='SornazSeedMedia/1.0 (contact: local-development)' }; break }
                catch { if ($attempt -eq 5) { throw }; Start-Sleep -Seconds ([Math]::Pow(2,$attempt)) }
            }
            Start-Sleep -Milliseconds 800
        }
        $image = [System.Drawing.Image]::FromFile($temp)
        try {
            $sourceRatio = $image.Width / $image.Height; $targetRatio = $width / $height
            if ($sourceRatio -gt $targetRatio) { $cropH=$image.Height; $cropW=[int]($cropH*$targetRatio); $x=[int](($image.Width-$cropW)/2); $y=0 }
            else { $cropW=$image.Width; $cropH=[int]($cropW/$targetRatio); $x=0; $y=[int](($image.Height-$cropH)/2) }
            $bitmap = New-Object System.Drawing.Bitmap($width,$height)
            try {
                $graphics=[System.Drawing.Graphics]::FromImage($bitmap)
                try { $graphics.DrawImage($image,[System.Drawing.Rectangle]::new(0,0,$width,$height),[System.Drawing.Rectangle]::new($x,$y,$cropW,$cropH),[System.Drawing.GraphicsUnit]::Pixel) }
                finally { $graphics.Dispose() }
                $codec=[System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object MimeType -eq 'image/jpeg'
                $parameters=New-Object System.Drawing.Imaging.EncoderParameters(1); $parameters.Param[0]=New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality,82L)
                $bitmap.Save($path,$codec,$parameters)
            } finally { $bitmap.Dispose() }
        } finally { $image.Dispose() }
    } finally { }
}
function Credit($page) {
    $meta=$page.imageinfo[0].extmetadata
    [ordered]@{ title=$page.title; source=$page.imageinfo[0].descriptionurl; author=$meta.Artist.value -replace '<[^>]+>',''; license=$meta.LicenseShortName.value; license_url=$meta.LicenseUrl.value }
}

$male = @([pscustomobject]@{pageid='mixkit-427';title='Musician playing drums on stage';imageinfo=@([pscustomobject]@{thumburl='https://assets.mixkit.co/videos/427/427-thumb-720-0.jpg';descriptionurl='https://mixkit.co/free-stock-video/musician-playing-drums-on-stage-427/';extmetadata=[pscustomobject]@{Artist=[pscustomobject]@{value='Mixkit'};LicenseShortName=[pscustomobject]@{value='Mixkit Video Free License'};LicenseUrl=[pscustomobject]@{value='https://mixkit.co/license/#videoFree'}}})})
$female = @([pscustomobject]@{pageid='mixkit-3597';title='Girl singing and playing guitar';imageinfo=@([pscustomobject]@{thumburl='https://assets.mixkit.co/videos/3597/3597-thumb-720-0.jpg';descriptionurl='https://mixkit.co/free-stock-video/girl-singing-into-a-microphone-and-playing-the-guitar-3597/';extmetadata=[pscustomobject]@{Artist=[pscustomobject]@{value='Mixkit'};LicenseShortName=[pscustomobject]@{value='Mixkit Video Free License'};LicenseUrl=[pscustomobject]@{value='https://mixkit.co/license/#videoFree'}}})})
$music = @($male[0],$female[0])
$credits=@()
for($i=1;$i -le 50;$i++) {
    $gender = if ($i % 2) { 'male' } else { 'female' }; $pool = if ($gender -eq 'male') { $male } else { $female }; $person=$pool[0]
    $profile=Join-Path $mediaRoot ('profiles/user-{0:d2}.jpg' -f $i); Save-CroppedImage $person $profile 720 720; $credits += (Credit $person) + @{ file=('profiles/user-{0:d2}.jpg' -f $i) }
    $coverSource=$music[($i*3)%$music.Count]; $cover=Join-Path $mediaRoot ('covers/user-{0:d2}.jpg' -f $i); Save-CroppedImage $coverSource $cover 1280 720; $credits += (Credit $coverSource) + @{ file=('covers/user-{0:d2}.jpg' -f $i) }
    for($g=1;$g -le $GalleryPerUser;$g++) { $source=$music[($i*7+$g*5)%$music.Count]; $file=('gallery/user-{0:d2}-{1:d2}.jpg' -f $i,$g); Save-CroppedImage $source (Join-Path $mediaRoot $file) 1200 900; $credits += (Credit $source) + @{ file=$file } }
}
$credits | ConvertTo-Json -Depth 5 | Set-Content -Encoding UTF8 (Join-Path $mediaRoot 'ATTRIBUTION.json')
$videoSources = @(
    @{ gender='male'; url='https://assets.mixkit.co/videos/427/427-360.mp4'; page='https://mixkit.co/free-stock-video/musician-playing-drums-on-stage-427/' },
    @{ gender='female'; url='https://assets.mixkit.co/videos/3597/3597-360.mp4'; page='https://mixkit.co/free-stock-video/girl-singing-into-a-microphone-and-playing-the-guitar-3597/' }
)
foreach ($source in $videoSources) {
    $cached=Join-Path $cacheRoot ($source.gender + '.mp4')
    if (!(Test-Path $cached)) { Invoke-WebRequest -Uri $source.url -OutFile $cached -Headers @{ 'User-Agent'='SornazSeedMedia/1.0' } }
    for($i=1;$i -le 20;$i++) { $userGender = if ($i % 2) {'male'} else {'female'}; if ($userGender -eq $source.gender) { Copy-Item -Force $cached (Join-Path $mediaRoot ('intro-videos/user-{0:d2}.mp4' -f $i)) } }
}
$videoSources | ConvertTo-Json | Set-Content -Encoding UTF8 (Join-Path $mediaRoot 'VIDEO-SOURCES.json')
Write-Output "Downloaded 50 profiles, 50 covers and $($GalleryPerUser*50) gallery images."
