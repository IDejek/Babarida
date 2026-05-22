<?php
/**
 * Interactive Map Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;
?>

<section class="map-section" id="map" aria-label="Dive Map">
    <div class="map-header">
        <div class="section-label">Our Waters</div>
        <h2 class="section-title">Dive Sites of North Sulawesi</h2>
        <p class="section-subtitle">Explore our four premier diving destinations stretching across the Bunaken Marine Park to the Lembeh Strait.</p>
    </div>
    <div class="map-container">
        <div class="map-svg-container">
            <svg viewBox="0 0 900 560" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="North Sulawesi dive map">
                <rect width="900" height="560" rx="16" fill="#002B4D"/>
                <path d="M0 200 Q80 180 120 220 Q160 260 140 300 Q120 340 80 320 Q40 300 0 320 Z" fill="#1A4A2E" opacity="0.6"/>
                <path d="M200 0 Q220 40 180 80 Q160 100 200 140 Q240 180 220 220 Q200 260 240 300 Q280 340 260 380 Q240 420 280 460 Q320 500 300 560 L200 560 Q220 500 180 460 Q140 420 160 380 Q180 340 140 300 Q100 260 120 220 Q140 180 100 140 Q60 100 80 60 Q100 20 200 0 Z" fill="#1A4A2E" opacity="0.7"/>
                <path d="M500 100 Q540 80 560 120 Q580 160 560 200 Q540 240 580 280 Q620 320 600 360 Q580 400 620 440 Q660 480 640 520 Q620 560 660 560 L500 560 Q520 500 480 460 Q440 420 460 380 Q480 340 440 300 Q400 260 420 220 Q440 180 400 140 Q360 100 400 60 Q440 20 500 100 Z" fill="#1A4A2E" opacity="0.6"/>
                <path d="M50 500 Q200 480 350 500 Q500 520 650 500 Q800 480 900 500" stroke="#0055AA" stroke-width="0.5" opacity="0.3"/>
                <path d="M0 100 Q150 80 300 100 Q450 120 600 100 Q750 80 900 100" stroke="#0055AA" stroke-width="0.5" opacity="0.3"/>
                <path d="M350 180 Q380 250 340 320" stroke="rgba(255,184,0,0.4)" stroke-width="2" stroke-dasharray="6 4" fill="none">
                    <animate attributeName="stroke-dashoffset" from="0" to="-20" dur="2s" repeatCount="indefinite"/>
                </path>
                <path d="M350 180 Q500 160 560 240" stroke="rgba(255,184,0,0.4)" stroke-width="2" stroke-dasharray="6 4" fill="none">
                    <animate attributeName="stroke-dashoffset" from="0" to="-20" dur="2s" repeatCount="indefinite"/>
                </path>
                <path d="M560 240 Q540 340 460 400" stroke="rgba(255,184,0,0.4)" stroke-width="2" stroke-dasharray="6 4" fill="none">
                    <animate attributeName="stroke-dashoffset" from="0" to="-20" dur="2s" repeatCount="indefinite"/>
                </path>
                <text x="310" y="170" fill="rgba(255,255,255,0.5)" font-size="11" font-family="Inter" font-weight="500">Bunaken</text>
                <text x="295" y="340" fill="rgba(255,255,255,0.5)" font-size="11" font-family="Inter" font-weight="500">Siladen</text>
                <text x="580" y="235" fill="rgba(255,255,255,0.5)" font-size="11" font-family="Inter" font-weight="500">Bangka</text>
                <text x="430" y="420" fill="rgba(255,255,255,0.5)" font-size="11" font-family="Inter" font-weight="500">Lembeh</text>
                <text x="250" y="290" fill="rgba(255,255,255,0.3)" font-size="10" font-family="Inter">Manado</text>
                <g transform="translate(820,80)">
                    <circle cx="0" cy="0" r="30" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>
                    <text x="0" y="-35" fill="rgba(255,255,255,0.4)" font-size="10" font-family="Inter" font-weight="700" text-anchor="middle">N</text>
                    <line x1="0" y1="-22" x2="0" y2="-10" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
                    <line x1="0" y1="10" x2="0" y2="22" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                    <polygon points="0,-10 -4,2 4,2" fill="rgba(255,184,0,0.8)"/>
                </g>
            </svg>
            <div class="map-pin" style="left:39%;top:32%;" tabindex="0" role="button" aria-label="Bunaken Island">
                <div class="map-pin-ring"></div>
                <div class="map-pin-dot"></div>
                <div class="map-pin-label">Bunaken Island</div>
            </div>
            <div class="map-pin" style="left:38%;top:60%;" tabindex="0" role="button" aria-label="Siladen">
                <div class="map-pin-ring" style="animation-delay:0.5s;"></div>
                <div class="map-pin-dot"></div>
                <div class="map-pin-label">Siladen</div>
            </div>
            <div class="map-pin" style="left:62%;top:42%;" tabindex="0" role="button" aria-label="Bangka Island">
                <div class="map-pin-ring" style="animation-delay:1s;"></div>
                <div class="map-pin-dot"></div>
                <div class="map-pin-label">Bangka Island</div>
            </div>
            <div class="map-pin" style="left:51%;top:72%;" tabindex="0" role="button" aria-label="Lembeh Strait">
                <div class="map-pin-ring" style="animation-delay:1.5s;"></div>
                <div class="map-pin-dot"></div>
                <div class="map-pin-label">Lembeh Strait</div>
            </div>
        </div>
    </div>
    <div class="map-info-cards">
        <div class="map-info-card reveal reveal-delay-1" tabindex="0"><h4>Bunaken</h4><p>Wall diving, 40m+ visibility, reef sharks, turtles, barracuda</p></div>
        <div class="map-info-card reveal reveal-delay-2" tabindex="0"><h4>Siladen</h4><p>Shallow reefs, snorkeling paradise, pristine coral gardens</p></div>
        <div class="map-info-card reveal reveal-delay-3" tabindex="0"><h4>Bangka</h4><p>Soft corals, pinnacles, nudibranchs, underwater caves</p></div>
        <div class="map-info-card reveal reveal-delay-4" tabindex="0"><h4>Lembeh</h4><p>Muck diving capital, frogfish, octopus, mimic octopus, rhinopias</p></div>
    </div>
</section>
