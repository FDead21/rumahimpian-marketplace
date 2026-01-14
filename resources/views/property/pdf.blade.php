<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $property->title }}</title>
    <style>
        /* (Your existing CSS styles remain exactly the same) */
        @page { margin: 0px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; margin: 0px; color: #1f2937; line-height: 1.4; }
        .hero { position: relative; width: 100%; height: 350px; background-color: #f3f4f6; }
        .hero-img { width: 100%; height: 100%; object-fit: cover; }
        .gallery { width: 100%; height: 120px; border-bottom: 4px solid #fff; }
        .gallery td { width: 33.33%; height: 120px; padding: 0; border-right: 2px solid #fff; background: #f3f4f6; overflow: hidden; }
        .gallery img { width: 100%; height: 100%; object-fit: cover; }
        .container { padding: 30px 40px; }
        .title { font-size: 26px; font-weight: bold; color: #111; margin: 0; }
        .badge { background: #4F46E5; color: white; padding: 4px 10px; font-size: 10px; font-weight: bold; border-radius: 4px; display: inline-block; vertical-align: middle; margin-left: 10px;}
        .address { font-size: 12px; color: #6b7280; margin-top: 5px; margin-bottom: 20px; }
        .price-box { background: #f3f4f6; padding: 10px 15px; border-radius: 8px; border-left: 5px solid #4F46E5; text-align: center; }
        .price-label { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .price-val { font-size: 20px; font-weight: 800; color: #4F46E5; }
        .features-table { width: 100%; border-spacing: 10px 0; margin: 0 -10px 20px -10px; }
        .feature-cell { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; text-align: center; width: 33.33%; }
        .feature-val { font-size: 18px; font-weight: bold; color: #111; display: block; }
        .feature-lbl { font-size: 9px; text-transform: uppercase; color: #6b7280; display: block; margin-top: 5px; }
        .section-title { font-size: 14px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; color: #111; }
        .desc-text { font-size: 12px; color: #4b5563; text-align: justify; margin-bottom: 20px; line-height: 1.5; }
        .details-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .details-table td { padding: 6px 0; border-bottom: 1px solid #eee; }
        .dt-label { font-weight: bold; color: #374151; }
        .dt-value { text-align: right; color: #111; }
        .footer { position: absolute; bottom: 0; left: 0; width: 100%; height: 130px; background-color: #111827; color: white; padding: 0; }
        .footer-table { width: 100%; padding: 20px 40px; border-collapse: collapse; }
        .agent-photo { width: 60px; height: 60px; border-radius: 50%; border: 2px solid #4F46E5; object-fit: cover; background: #333; }
        .agent-name { font-size: 16px; font-weight: bold; margin-bottom: 2px; color: #fff; }
        .agent-role { font-size: 10px; text-transform: uppercase; color: #9ca3af; letter-spacing: 1px; }
        .agent-contact { font-size: 10px; color: #d1d5db; margin-top: 6px; line-height: 1.4; }
        .qr-box { background: white; padding: 3px; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>

    @php
        // (Your existing Image/QR Logic remains the same)
        $allImages = $property->media;
        $heroImage = null;
        foreach ($allImages as $img) {
            $path = public_path('storage/' . $img->file_path);
            if (file_exists($path)) {
                list($width, $height) = getimagesize($path);
                if ($height > 0 && ($width / $height) < 1.9) {
                    $heroImage = $img;
                    break; 
                }
            }
        }
        if (!$heroImage) {
             foreach ($allImages as $img) {
                if (file_exists(public_path('storage/' . $img->file_path))) {
                    $heroImage = $img;
                    break;
                }
             }
        }
        $base64Hero = null;
        if($heroImage) {
            try {
                $path = public_path('storage/' . $heroImage->file_path);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64Hero = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } catch (\Exception $e) { }
        }
        $heroId = $heroImage ? $heroImage->id : 0;
        $galleryImages = $property->media->where('id', '!=', $heroId)->take(3);

        $qrBase64 = null;
        try {
            $currentUrl = route('property.show', [$property->id, $property->slug]);
            $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($currentUrl);
            $qrContent = file_get_contents($qrApiUrl);
            if ($qrContent) {
                $qrBase64 = 'data:image/png;base64,' . base64_encode($qrContent);
            }
        } catch (\Exception $e) { }
    @endphp

    <div class="hero">
        @if($base64Hero)
            <img src="{{ $base64Hero }}" class="hero-img">
        @else
            <div style="width:100%; height:100%; background:#e5e7eb; display:flex; align-items:center; justify-content:center;">
                <span style="color:#6b7280; font-size:12px;">{{ __('No Image Available') }}</span>
            </div>
        @endif
    </div>

    @if($galleryImages->count() > 0)
    <table class="gallery" cellspacing="0" cellpadding="0">
        <tr>
            @foreach($galleryImages as $image)
            <td>
                <img src="{{ public_path('storage/' . $image->file_path) }}">
            </td>
            @endforeach
            @for($i = $galleryImages->count(); $i < 3; $i++)
                <td style="background: #f3f4f6;"></td>
            @endfor
        </tr>
    </table>
    @endif

    <div class="container">
        <table width="100%">
            <tr>
                <td width="70%" valign="top">
                    <h1 class="title">{{ Str::limit($property->title, 40) }}</h1>
                    <div class="address">
                        <b>{{ __('Location') }}:</b> {{ $property->district }}, {{ $property->city }}
                        <span class="badge">{{ __(ucfirst(strtolower($property->listing_type))) }}</span>
                    </div>
                </td>
                <td width="30%" valign="top" align="right">
                    <div class="price-box">
                        <div class="price-label">{{ __('Price') }}</div>
                        <div class="price-val">{{ $property->price >= 1000000000 ? number_format($property->price / 1000000000, 1) . ' M' : number_format($property->price / 1000000, 0) . ' Juta' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="features-table">
            <tr>
                <td class="feature-cell"><span class="feature-val">{{ $property->bedrooms }}</span><span class="feature-lbl">{{ __('Bedrooms') }}</span></td>
                <td class="feature-cell"><span class="feature-val">{{ $property->bathrooms }}</span><span class="feature-lbl">{{ __('Bathrooms') }}</span></td>
                <td class="feature-cell"><span class="feature-val">{{ $property->building_area }} m²</span><span class="feature-lbl">{{ __('Building Size') }}</span></td>
            </tr>
        </table>

        <div class="section-title">{{ __('Description') }}</div>
        <div class="desc-text">{{ Str::limit($property->description, 350) }}</div>

        <div class="section-title">{{ __('Details') }}</div>
        <table class="details-table">
            <tr><td class="dt-label">Listing ID</td><td class="dt-value">#{{ $property->id }}</td></tr>
            <tr><td class="dt-label">{{ __('Property Type') }}</td><td class="dt-value">{{ __($property->property_type ?? 'Residential') }}</td></tr>
            @if($property->specifications)
                @foreach($property->specifications as $key => $value)
                <tr>
                    <td class="dt-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td class="dt-value">{{ $value }}</td>
                </tr>
                @endforeach
            @endif
        </table>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td valign="middle">
                    <div class="agent-name">{{ $property->user->name }}</div>
                    <div class="agent-role">{{ __('Verified Agent') }}</div>
                    <div class="agent-contact">Phone: {{ $property->user->phone_number }}<br>Email: {{ $property->user->email }}</div>
                </td>
                <td width="100" align="right" valign="middle">
                    <table cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="center">
                                <div class="qr-box">
                                    @if($qrBase64)
                                        <img src="{{ $qrBase64 }}" width="60" style="display:block;">
                                    @else
                                        <div style="width:60px; height:60px; background:#eee;"></div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div style="font-size: 8px; color: #9ca3af; margin-top: 5px;">{{ __('Scan to view') }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>