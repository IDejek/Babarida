/**
 * Babarida Dive Center — Main Frontend JS
 *
 * @package Babarida_Dive_Center
 * @version 1.0.0
 * @author Iqbal Tombinawa <tombinawaiqbal@gmail.com>
 */

(function() {
    'use strict';

    /* ============================================
       PRELOADER
       ============================================ */
    window.addEventListener('load', function() {
        setTimeout(function() {
            var p = document.getElementById('preloader');
            if (p) p.classList.add('hidden');
            document.body.style.overflow = '';
        }, 2800);
    });
    setTimeout(function() {
        var p = document.getElementById('preloader');
        if (p && !p.classList.contains('hidden')) {
            p.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }, 5000);

    /* ============================================
       AMBIENT BUBBLES
       ============================================ */
    var bubblesContainer = document.getElementById('ambientBubbles');
    if (bubblesContainer) {
        for (var i = 0; i < 12; i++) {
            var bubble = document.createElement('div');
            bubble.className = 'ambient-bubble';
            var size = Math.max(8, Math.random() * 30);
            bubble.style.width = size + 'px';
            bubble.style.height = size + 'px';
            bubble.style.left = (Math.random() * 100) + '%';
            bubble.style.animationDuration = (8 + Math.random() * 15) + 's';
            bubble.style.animationDelay = (Math.random() * 10) + 's';
            bubblesContainer.appendChild(bubble);
        }
    }

    /* ============================================
       WORLD CLOCKS
       ============================================ */
    function updateClocks() {
        document.querySelectorAll('.clock-time[data-tz]').forEach(function(el) {
            try {
                var tz = el.getAttribute('data-tz');
                el.textContent = new Date().toLocaleTimeString('en-GB', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit',
                    hour12: false, timeZone: tz
                });
            } catch(e) { el.textContent = '--:--'; }
        });
    }
    updateClocks();
    setInterval(updateClocks, 1000);

    /* ============================================
       SCROLL HEADER
       ============================================ */
    var topBar = document.getElementById('topBar');
    var mainHeader = document.getElementById('mainHeader');
    var lastScrollY = 0;
    var ticking = false;

    function onScroll() {
        var scrollY = window.scrollY;

        if (scrollY > 60) {
            mainHeader.classList.add('scrolled');
        } else {
            mainHeader.classList.remove('scrolled');
        }

        if (scrollY > 200 && scrollY > lastScrollY) {
            topBar.classList.add('scrolled-up');
            mainHeader.style.top = '0';
        } else {
            topBar.classList.remove('scrolled-up');
            mainHeader.style.top = topBar.classList.contains('scrolled-up') ? '0' : '44px';
        }

        var btt = document.getElementById('backToTop');
        if (btt) {
            if (scrollY > 600) btt.classList.add('visible');
            else btt.classList.remove('visible');
        }

        if (scrollY > window.innerHeight * 0.8) {
            var ww = document.getElementById('weatherWidget');
            var cs = document.getElementById('currencySwitcher');
            if (ww) ww.classList.add('visible');
            if (cs) cs.classList.add('visible');
        }

        lastScrollY = scrollY;
        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) { requestAnimationFrame(onScroll); ticking = true; }
    }, { passive: true });

    document.getElementById('backToTop').addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* ============================================
       MOBILE MENU
       ============================================ */
    var mobileToggle = document.getElementById('mobileToggle');
    var mobileMenu = document.getElementById('mobileMenu');
    var isMenuOpen = false;

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function() {
            isMenuOpen = !isMenuOpen;
            mobileMenu.classList.toggle('open', isMenuOpen);
            mobileToggle.classList.toggle('active', isMenuOpen);
            mobileToggle.setAttribute('aria-expanded', isMenuOpen);
            document.body.style.overflow = isMenuOpen ? 'hidden' : '';
        });

        document.querySelectorAll('.mobile-nav-link[data-submenu]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var subId = this.getAttribute('data-submenu');
                var subMenu = document.getElementById(subId);
                var isOpen = subMenu && subMenu.classList.contains('open');
                document.querySelectorAll('.mobile-sub-menu').forEach(function(s) { s.classList.remove('open'); });
                document.querySelectorAll('.mobile-nav-link').forEach(function(b) { b.classList.remove('active'); });
                if (!isOpen && subMenu) {
                    subMenu.classList.add('open');
                    this.classList.add('active');
                }
            });
        });

        mobileMenu.querySelectorAll('a[href]').forEach(function(link) {
            link.addEventListener('click', function() {
                if (this.getAttribute('href') === '#') return;
                isMenuOpen = false;
                mobileMenu.classList.remove('open');
                mobileToggle.classList.remove('active');
                mobileToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });
    }

    /* ============================================
       SCROLL REVEAL
       ============================================ */
    var revealElements = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var revealObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        revealElements.forEach(function(el) { revealObs.observe(el); });
    } else {
        revealElements.forEach(function(el) { el.classList.add('visible'); });
    }

    /* ============================================
       TESTIMONIAL SLIDER
       ============================================ */
    var testiTrack = document.getElementById('testiTrack');
    var testiCards = testiTrack ? testiTrack.querySelectorAll('.testi-card') : [];
    var currentTesti = 0;
    var testiDotsContainer = document.getElementById('testiDots');

    if (testiDotsContainer) {
        for (var d = 0; d < testiCards.length; d++) {
            var dot = document.createElement('button');
            dot.className = 'testi-dot' + (d === 0 ? ' active' : '');
            dot.setAttribute('aria-label', 'Review ' + (d + 1));
            dot.dataset.index = d;
            testiDotsContainer.appendChild(dot);
        }
    }

    function goToTesti(idx) {
        currentTesti = idx;
        if (testiTrack) testiTrack.style.transform = 'translateX(-' + (currentTesti * 100) + '%)';
        document.querySelectorAll('.testi-dot').forEach(function(dd, ii) {
            dd.classList.toggle('active', ii === currentTesti);
        });
    }

    var prevBtn = document.getElementById('testiPrev');
    var nextBtn = document.getElementById('testiNext');
    if (prevBtn) prevBtn.addEventListener('click', function() {
        goToTesti(currentTesti > 0 ? currentTesti - 1 : testiCards.length - 1);
    });
    if (nextBtn) nextBtn.addEventListener('click', function() {
        goToTesti(currentTesti < testiCards.length - 1 ? currentTesti + 1 : 0);
    });
    if (testiDotsContainer) testiDotsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('testi-dot')) goToTesti(parseInt(e.target.dataset.index));
    });
    setInterval(function() {
        goToTesti(currentTesti < testiCards.length - 1 ? currentTesti + 1 : 0);
    }, 6000);

    // Touch swipe
    if (testiTrack) {
        var touchStartX = 0;
        testiTrack.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        testiTrack.addEventListener('touchend', function(e) {
            var diff = touchStartX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 50) {
                goToTesti(diff > 0
                    ? (currentTesti < testiCards.length - 1 ? currentTesti + 1 : 0)
                    : (currentTesti > 0 ? currentTesti - 1 : testiCards.length - 1)
                );
            }
        }, { passive: true });
    }

    /* ============================================
       PRICING TABLE
       ============================================ */
    var currentCurrency = 'USD';
    var currencyRates = { USD: 1, EUR: 0.92, SGD: 1.34, AUD: 1.53, IDR: 15800 };
    var currencySymbols = { USD: '$', EUR: '\u20AC', SGD: 'S$', AUD: 'A$', IDR: 'Rp ' };

    function formatPrice(usd) {
        var converted = Math.round(usd * currencyRates[currentCurrency]);
        if (currentCurrency === 'IDR') return currencySymbols[currentCurrency] + converted.toLocaleString('en-US');
        return currencySymbols[currentCurrency] + converted.toLocaleString('en-US');
    }

    function renderPricing(filter) {
        var tbody = document.getElementById('pricingTableBody');
        if (!tbody) return;
        var html = '';
        for (var m = 0; m < 24; m++) {
            var date = new Date(); date.setMonth(date.getMonth() + m);
            var monthNum = date.getMonth();
            var year = date.getFullYear();
            var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            var seasonKey = 'low';
            if (monthNum >= 3 && monthNum <= 5) seasonKey = 'high';
            if (monthNum >= 6 && monthNum <= 8) seasonKey = 'peak';
            if (monthNum >= 9 && monthNum <= 10) seasonKey = 'high';
            var prices = { low: {dt:65,lb:1800,cr:380,l:'Low Season',c:'season-low'}, high: {dt:85,lb:2200,cr:420,l:'High Season',c:'season-high'}, peak: {dt:110,lb:2800,cr:480,l:'Peak Season',c:'season-peak'} };
            var s = prices[seasonKey];
            html += '<tr><td style="font-weight:600;">' + monthNames[monthNum] + ' ' + year + '</td>';
            html += '<td><span class="season-badge ' + s.c + '">' + s.l + '</span></td>';
            html += '<td class="price-cell" data-type="day-trip">' + formatPrice(s.dt) + '</td>';
            html += '<td class="price-cell" data-type="liveaboard">' + formatPrice(s.lb) + '</td>';
            html += '<td class="price-cell" data-type="course">' + formatPrice(s.cr) + '</td>';
            html += '<td class="book-cell"><a href="#bookingModal" class="btn btn-primary btn-sm">Book</a></td></tr>';
        }
        tbody.innerHTML = html;
        if (filter && filter !== 'all') {
            tbody.querySelectorAll('.price-cell').forEach(function(cell) {
                if (cell.dataset.type !== filter) cell.style.opacity = '0.3';
            });
        }
    }
    renderPricing('all');

    document.querySelectorAll('.pricing-filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.pricing-filter-btn').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            renderPricing(this.dataset.filter);
        });
    });

    // Currency switcher
    var currencies = ['USD','EUR','SGD','AUD','IDR'];
    var currencyIndex = 0;
    var currencyBtn = document.getElementById('currencyBtn');
    var currencyLabel = document.getElementById('currencyLabel');
    if (currencyBtn) currencyBtn.addEventListener('click', function() {
        currencyIndex = (currencyIndex + 1) % currencies.length;
        currentCurrency = currencies[currencyIndex];
        if (currencyLabel) currencyLabel.textContent = currentCurrency;
        renderPricing(document.querySelector('.pricing-filter-btn.active') ? document.querySelector('.pricing-filter-btn.active').dataset.filter : 'all');
        showToast('Currency changed to ' + currentCurrency, 'success');
    });

    /* ============================================
       FAQ ACCORDION
       ============================================ */
    document.querySelectorAll('.faq-question').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var item = this.parentElement;
            var isActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(function(f) {
                f.classList.remove('active');
                f.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });
            if (!isActive) {
                item.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });

    /* ============================================
       BOOKING MODAL
       ============================================ */
    var bookingModal = document.getElementById('bookingModal');

    document.querySelectorAll('a[href="#bookingModal"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (bookingModal) {
                bookingModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                var dateInput = document.getElementById('bookDate');
                if (dateInput) dateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
            }
        });
    });

    function closeModal() {
        if (bookingModal) {
            bookingModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    var modalClose = document.getElementById('modalClose');
    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (bookingModal) bookingModal.addEventListener('click', function(e) { if (e.target === bookingModal) closeModal(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            var chatPanel = document.getElementById('aiChatPanel');
            if (chatPanel) chatPanel.classList.remove('open');
        }
    });

    // Booking form AJAX
    var bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var fn = document.getElementById('bookFirstName').value.trim();
            var ln = document.getElementById('bookLastName').value.trim();
            var em = document.getElementById('bookEmail').value.trim();
            var ph = document.getElementById('bookPhone').value.trim();
            var dest = document.getElementById('bookDestination').value;
            var dt = document.getElementById('bookDate').value;

            if (!fn || !ln || !em || !ph || !dest || !dt) { showToast('Please fill in all required fields.', 'error'); return; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { showToast('Please enter a valid email.', 'error'); return; }

            var submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;

            var formData = new FormData(this);
            formData.append('action', 'babarida_submit_booking');
            formData.append('nonce', babaridaData ? babaridaData.nonce : '');

            fetch(babaridaData ? babaridaData.ajaxUrl : '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData
            }).then(function(r) { return r.json(); }).then(function(res) {
                closeModal();
                showToast(res.data && res.data.message ? res.data.message : 'Booking sent!', res.success ? 'success' : 'error');
                bookingForm.reset();
            }).catch(function() {
                closeModal();
                showToast('Booking inquiry sent! We\'ll contact you within 24 hours.', 'success');
                bookingForm.reset();
            }).finally(function() {
                submitBtn.innerHTML = 'Send Booking Inquiry <i class="fa-solid fa-paper-plane"></i>';
                submitBtn.disabled = false;
            });
        });
    }

    /* ============================================
       CHECK-IN FORM
       ============================================ */
    var checkinForm = document.getElementById('checkinForm');
    if (checkinForm) {
        checkinForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var inputs = this.querySelectorAll('input');
            var valid = true;
            inputs.forEach(function(inp) {
                if (!inp.value.trim()) { valid = false; inp.style.borderColor = '#EF4444'; }
                else { inp.style.borderColor = ''; }
            });
            if (!valid) { showToast('Please fill in all fields.', 'error'); return; }
            showToast('Check-in request submitted. Our team will assist you shortly.', 'success');
            this.reset();
        });
    }

    /* ============================================
       NEWSLETTER
       ============================================ */
    var nlForm = document.getElementById('newsletterForm');
    if (nlForm) {
        nlForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = this.querySelector('input[type="email"]').value.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('Please enter a valid email.', 'error'); return; }

            var formData = new FormData();
            formData.append('action', 'babarida_subscribe_newsletter');
            formData.append('email', email);
            formData.append('nonce', babaridaData ? babaridaData.nonce : '');

            fetch(babaridaData ? babaridaData.ajaxUrl : '/wp-admin/admin-ajax.php', {
                method: 'POST', body: formData
            }).catch(function() {});
            showToast('Welcome aboard! You\'re now subscribed.', 'success');
            this.reset();
        });
    }

    /* ============================================
       TOAST NOTIFICATIONS
       ============================================ */
    function showToast(message, type) {
        type = type || 'success';
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        var icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
        toast.innerHTML = '<i class="fa-solid ' + icon + '"></i><span>' + message + '</span>';
        container.appendChild(toast);
        requestAnimationFrame(function() { toast.classList.add('show'); });
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() { toast.remove(); }, 400);
        }, 4000);
    }

    /* ============================================
       AI CHAT ASSISTANT
       ============================================ */
    var chatToggle = document.getElementById('aiChatToggle');
    var chatPanel = document.getElementById('aiChatPanel');
    var chatMessages = document.getElementById('chatMessages');
    var chatInput = document.getElementById('chatInput');
    var chatSend = document.getElementById('chatSend');

    if (chatToggle) chatToggle.addEventListener('click', function() {
        if (chatPanel) chatPanel.classList.toggle('open');
    });

    function sendUserMsg(text) {
        if (!chatMessages) return;
        var div = document.createElement('div');
        div.className = 'chat-msg user';
        div.textContent = text;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        var qr = document.getElementById('quickReplies');
        if (qr) qr.style.display = 'none';
    }

    function showBotReply(text) {
        if (!chatMessages) return;
        // Typing indicator
        var typing = document.createElement('div');
        typing.className = 'chat-msg bot';
        typing.id = 'typingIndicator';
        typing.innerHTML = '<i class="fa-solid fa-ellipsis fa-fade" style="color:#94A3B8;"></i>';
        chatMessages.appendChild(typing);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        setTimeout(function() {
            var ti = document.getElementById('typingIndicator');
            if (ti) ti.remove();
            var botDiv = document.createElement('div');
            botDiv.className = 'chat-msg bot';
            botDiv.textContent = text;
            chatMessages.appendChild(botDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 600 + Math.random() * 600);
    }

    function processChat(msg) {
        sendUserMsg(msg);
        // Use AJAX if available, else fallback
        if (babaridaData && babaridaData.ajaxUrl) {
            var formData = new FormData();
            formData.append('action', 'babarida_chat_reply');
            formData.append('message', msg);
            formData.append('nonce', babaridaData.nonce);
            fetch(babaridaData.ajaxUrl, { method: 'POST', body: formData })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    showBotReply(res.data && res.data.reply ? res.data.reply : 'Thank you for your question! Please contact us on WhatsApp for detailed assistance.');
                })
                .catch(function() {
                    showBotReply(getFallbackReply(msg));
                });
        } else {
            showBotReply(getFallbackReply(msg));
        }
    }

    function getFallbackReply(msg) {
        var l = msg.toLowerCase();
        if (l.includes('bunaken')) return 'Bunaken is famous for its vertical walls, crystal-clear waters, reef sharks, turtles, and over 390 coral species. We offer day trips, dive & stay packages, and liveaboard routes.';
        if (l.includes('liveaboard')) return 'We have three vessels: Babarida Phinisi I (from $2,400), Babarida Explorer (from $1,600), and Babarida Catamaran (from $980). All include meals, dives, and equipment.';
        if (l.includes('price') || l.includes('cost')) return 'Day trips from $65, liveaboards from $980, SSI courses from $380. Check our Monthly Price List for the full schedule!';
        if (l.includes('lembeh')) return 'Lembeh Strait is the world\'s muck diving capital — hairy frogfish, mimic octopus, rhinopias, and countless critters. Perfect for macro photography!';
        if (l.includes('book')) return 'Fill out our booking form or contact us on WhatsApp at +62 895 8019 60359 for instant assistance!';
        if (l.includes('ssi') || l.includes('course')) return 'We offer full SSI programs: Open Water ($380-$480), Advanced ($300), Rescue ($350), and specialty courses.';
        if (l.includes('siladen')) return 'Siladen offers crystal-clear waters, pristine coral gardens, and excellent snorkeling. Perfect for families!';
        if (l.includes('bangka')) return 'Bangka features dramatic underwater landscapes, soft coral gardens, pinnacles, and excellent macro life.';
        return 'Thanks for your question! Contact us on WhatsApp (+62 895 8019 60359) or email (info@babaridadive.com) for detailed assistance.';
    }

    if (chatSend) chatSend.addEventListener('click', function() {
        var msg = chatInput ? chatInput.value.trim() : '';
        if (msg) { processChat(msg); if (chatInput) chatInput.value = ''; }
    });
    if (chatInput) chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            var msg = this.value.trim();
            if (msg) { processChat(msg); this.value = ''; }
        }
    });

    var quickReplies = document.getElementById('quickReplies');
    if (quickReplies) quickReplies.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-reply-btn')) {
            processChat(e.target.getAttribute('data-msg'));
        }
    });

    /* ============================================
       WEATHER WIDGET
       ============================================ */
    var weatherClose = document.getElementById('weatherClose');
    if (weatherClose) weatherClose.addEventListener('click', function() {
        document.getElementById('weatherWidget').classList.remove('visible');
    });

    function updateWeather() {
        var hour = new Date().getHours();
        var conds = [
            {icon:'\u2600\uFE0F',desc:'Sunny',temp:31,water:30,vis:'30m',wind:'8 km/h',hum:'72%'},
            {icon:'\u26C5',desc:'Partly Cloudy',temp:30,water:29,vis:'25m',wind:'12 km/h',hum:'78%'},
            {icon:'\uD83C\uDF24\uFE0F',desc:'Mostly Sunny',temp:32,water:30,vis:'35m',wind:'6 km/h',hum:'70%'},
            {icon:'\u2601\uFE0F',desc:'Cloudy',temp:29,water:28,vis:'20m',wind:'15 km/h',hum:'82%'}
        ];
        var w = conds[hour < 10 ? 0 : hour < 14 ? 2 : hour < 18 ? 1 : 3];
        w.temp += Math.round((Math.random() - 0.5) * 2);
        var wi = document.getElementById('weatherIcon');
        var wt = document.getElementById('weatherTemp');
        var wd = document.getElementById('weatherDesc');
        var ww = document.getElementById('waterTemp');
        var wv = document.getElementById('visibility');
        var ws = document.getElementById('windSpeed');
        var wh = document.getElementById('humidity');
        if (wi) wi.textContent = w.icon;
        if (wt) wt.textContent = w.temp + '\u00B0';
        if (wd) wd.textContent = w.desc;
        if (ww) ww.textContent = w.water + '\u00B0C';
        if (wv) wv.textContent = w.vis;
        if (ws) ws.textContent = w.wind;
        if (wh) wh.textContent = w.hum;
    }
    updateWeather();
    setInterval(updateWeather, 300000);

    /* ============================================
       LANGUAGE SWITCHER
       ============================================ */
    document.querySelectorAll('.lang-switch button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.lang-switch button').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            showToast(this.dataset.lang === 'id' ? 'Bahasa Indonesia segera tersedia.' : 'Language set to English.', 'success');
        });
    });

    /* ============================================
       HERO VIDEO FALLBACK
       ============================================ */
    var heroVideo = document.querySelector('.hero-video-bg video');
    if (heroVideo) {
        heroVideo.addEventListener('error', function() {
            document.querySelector('.hero-video-bg').style.display = 'none';
        });
        setTimeout(function() {
            if (heroVideo.readyState < 2) {
                document.querySelector('.hero-video-bg').style.opacity = '0';
            }
        }, 3000);
    }

    /* ============================================
       SMOOTH SCROLL
       ============================================ */
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            if (href === '#' || href === '#bookingModal') return;
            var target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                var top = target.getBoundingClientRect().top + window.scrollY - 120;
                window.scrollTo({ top: top, behavior: 'smooth' });
            }
        });
    });

    /* ============================================
       HERO PARALLAX
       ============================================ */
    var heroContent = document.querySelector('.hero-content');
    if (heroContent && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.addEventListener('scroll', function() {
            var sy = window.scrollY;
            if (sy < window.innerHeight) {
                heroContent.style.transform = 'translateY(' + (sy * 0.3) + 'px)';
                heroContent.style.opacity = 1 - (sy / (window.innerHeight * 0.8));
            }
        }, { passive: true });
    }

    /* ============================================
       ACTIVE NAV
       ============================================ */
    var sections = document.querySelectorAll('section[id]');
    if ('IntersectionObserver' in window) {
        var navObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var id = entry.target.id;
                    document.querySelectorAll('.nav-link').forEach(function(link) {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + id) link.classList.add('active');
                    });
                }
            });
        }, { threshold: 0.3, rootMargin: '-100px 0px -50% 0px' });
        sections.forEach(function(s) { navObs.observe(s); });
    }

    /* ============================================
       COUNTER ANIMATION
       ============================================ */
    var badgeNumber = document.querySelector('.badge-number');
    if (badgeNumber && 'IntersectionObserver' in window) {
        var counterObs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var target = 15, cur = 0;
                    var timer = setInterval(function() {
                        cur++;
                        if (cur >= target) { cur = target; clearInterval(timer); }
                        badgeNumber.textContent = cur + '+';
                    }, 120);
                    counterObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        counterObs.observe(badgeNumber);
    }

    /* ============================================
       PRICING FILTER BUTTONS (dynamic pages)
       ============================================ */
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('pricing-filter-btn')) {
            document.querySelectorAll('.pricing-filter-btn').forEach(function(b) { b.classList.remove('active'); });
            e.target.classList.add('active');
        }
    });

})();
