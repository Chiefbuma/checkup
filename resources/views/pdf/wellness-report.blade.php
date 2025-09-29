<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wellness Report - {{ $record ? $record->first_name . ' ' . $record->surname : 'Unknown Patient' }}</title>
    <style>
        body { font-family: "Calibri", sans-serif; font-size: 14px; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .report-container { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { width: 50%; display: block; margin: 0 auto; }
        .date { text-align: right; font-size: 14px; margin-top: 5px; }
        .section-title { font-weight: bold; margin: 15px 0 10px; font-size: 15px; text-decoration: underline; color: blue; }
        .section-content { margin-bottom: 15px; }
        .section-content p { margin: 5px 0; }
        .footer { text-align: center; margin-top: 30px; border-top: 1px solid #000; font-size: 12px; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="header">
            <!-- Replace with your base64 encoded image -->
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/wide-logo.png'))) }}" alt="Wide Logo" class="logo">
            <div class="date">{{ $currentDate }}</div>
        </div>

        <div class="section-title">INDIVIDUAL WELLNESS REPORT:</div>
        <div class="section-content">
            <p>
                <strong>{{ $record ? $record->first_name . ' ' . $record->surname : 'Unknown Patient' }}</strong>
                {{ $record ? $record->email : 'N/A' }}
            </p>
        </div>

        <div class="section-title">Screening Results</div>
        <div class="section-content">
            <p><strong>Blood Pressure:</strong> {{ $vital ? $vital->bp_systolic . '/' . $vital->bp_diastolic . ' mmHg' : 'N/A' }}</p>
            <p><strong>Pulse:</strong> {{ $vital ? $vital->pulse . ' bpm' : 'N/A' }}</p>
            <p><strong>Temperature:</strong> {{ $vital ? $vital->temp : 'N/A' }}</p>
            <p><strong>Weight:</strong> {{ $nutrition ? $nutrition->weight . ' kgs' : 'N/A' }}</p>
            <p><strong>Height:</strong> {{ $nutrition ? $nutrition->height . ' cm' : 'N/A' }}</p>
            <p><strong>BMI:</strong> {{ $nutrition ? number_format($nutrition->bmi, 1) : 'N/A' }}</p>
            <p><strong>Blood sugar:</strong> {{ $vital ? $vital->rbs . ' mg/dL' : 'N/A' }}</p>
            <p><strong>Body fat percentage:</strong> {{ $nutrition ? $nutrition->body_fat_percent . '%' : 'N/A' }}</p>
            <p><strong>Healthy weight for height range (kgs):</strong> 
                {{ $nutrition ? number_format($nutrition->lower_limit_weight, 1) . ' - ' . number_format($nutrition->weight_limit_weight, 1) : 'N/A' }}
            </p>
        </div>

        <div class="section-title">Discussion Summary</div>
        <div class="section-content">
            @if($record->goals && $record->goals->count())
                @foreach($record->goals as $goal)
                    @if(!empty($goal->discussion))
                        <p>{{ $goal->discussion }}</p>
                    @endif
                @endforeach
            @else
                <p>No discussion summary available.</p>
            @endif
        </div>

        <div class="section-title">Personalized Health Goals</div>
        <div class="section-content">
            @if($record->goals && $record->goals->count())
                @foreach($record->goals as $goal)
                    @if(!empty($goal->goal))
                        <p>{{ $goal->goal }}</p>
                    @endif
                @endforeach
            @else
                <p>No personalized health goals available.</p>
            @endif
        </div>

        
        <div class="footer">
        <div class="section-title">Dr. Mymoona Mohammed</div>
            <p>FCB Mihrab • Mezzanine №2</p>
            <p>Lenana Road • Kilimani • NRB</p>
            <p>www.tariahealth.com</p>
            <p>Phone: 0706 487 965</p>
        </div>
    </div>
</body>
</html>