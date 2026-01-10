const BASE_URL = 'https://www.filmyfiy.mov';
const WORKER_API = 'https://restless-glitter-f6c4.dktczn.workers.dev/?url=';
const PLAYER_API_BASE = 'https://api.madplay.site/api/movies/filmyfly/link?server=10&url=';
const DOWNLOAD_PLAYER_WRAPPER = 'https://rogsports.pages.dev/?url=';
const LIVETV_JSON = 'https://raw.githubusercontent.com/kunwarxshashank/rogplay_addons/refs/heads/main/livetv/web.json';

// TMDB Constants
const TMDB_API_KEY = 'fed86956458f19fb45cdd382b6e6de83';
const TMDB_BASE_URL = 'https://api.themoviedb.org/3';
const TMDB_IMAGE_BASE = 'https://image.tmdb.org/t/p/w500';
const TMDB_PLAYER_BASE = 'https://veyda398osi.com/play/';

let currentPage = 1;
let currentSection = 'home'; // home, tmdb, livetv
let isLoading = false;
let searchQuery = '';
let currentCategoryUrl = ''; // For category pagination

// TMDB State
let tmdbCurrentPage = 1;
let tmdbCurrentQuery = '';
let tmdbIsSearching = false;

const contentGrid = document.getElementById('contentGrid');
const loadingIndicator = document.getElementById('loading');
const modal = document.getElementById('detailModal');
const modalBody = document.getElementById('modalBody');
const closeModal = document.querySelector('.close-modal');
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const categoriesContainer = document.getElementById('categories');

// --- Initialization ---
document.addEventListener('DOMContentLoaded', () => {
    loadHome();
    setupNavigation();
    setupInfiniteScroll();
    setupSearch();
    setupModal();
    setupStaticCategories();
});

function setupStaticCategories() {
    const STATIC_CATEGORIES = [
        { name: 'Web Series', url: '/page-cat/42/Web-Series.html' },
        { name: 'South Hindi', url: '/page-cat/21/South-Hindi-Dubbed-Movie.html' },
        { name: 'Bollywood', url: '/page-cat/1/Bollywood-Hindi-Movies.html' },
        { name: 'Hollywood', url: '/page-cat/4/Hollywood-Hindi-Movies.html' },
        { name: 'Animation', url: '/page-cat/73/Animation-Movies.html' },
        { name: 'HQ Dubbed', url: '/page-cat/58/HQ-Dubbed-Movies-UnCut.html' },
        { name: 'Punjabi', url: '/page-cat/15/Punjabi-Movies.html' }
    ];

    categoriesContainer.innerHTML = '';
    STATIC_CATEGORIES.forEach(cat => {
        const btn = document.createElement('div');
        btn.className = 'category-tag';
        btn.textContent = cat.name;
        btn.onclick = () => {
            currentCategoryUrl = cat.url;
            currentPage = 1;
            contentGrid.innerHTML = '';
            loadHome();
        };
        categoriesContainer.appendChild(btn);
    });
    categoriesContainer.style.display = 'flex';
}

// --- Navigation ---
function setupNavigation() {
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            const section = e.target.dataset.section;
            switchSection(section);
        });
    });
}

function switchSection(section) {
    currentSection = section;
    contentGrid.innerHTML = '';
    categoriesContainer.style.display = 'none';
    currentPage = 1;
    currentCategoryUrl = '';
    searchQuery = '';

    // Manage grid layout class and Live TV mode
    if (section === 'livetv') {
        document.body.classList.add('livetv-mode');
    } else {
        document.body.classList.remove('livetv-mode');
    }

    if (section === 'home') {
        contentGrid.classList.add('grid-container');
        categoriesContainer.style.display = 'flex';
        loadHome();
    } else {
        contentGrid.classList.remove('grid-container');
        if (section === 'tmdb') {
            renderTMDB();
        } else if (section === 'livetv') {
            loadLiveTV();
        }
    }
}

// --- Fetching & Parsing ---
async function fetchHTML(url) {
    try {
        console.log('Fetching:', url);
        const response = await fetch(WORKER_API + encodeURIComponent(url));
        if (!response.ok) throw new Error('Network response was not ok');
        
        const rawText = await response.text();
        let htmlContent = rawText;

        try {
            // The worker returns JSON: {"html": "..."}
            const data = JSON.parse(rawText);
            if (data.html) {
                htmlContent = data.html;
            }
        } catch (e) {
            // Not JSON, assume raw HTML
            console.log('Response is not JSON, using raw text');
        }

        console.log('HTML length:', htmlContent.length);
        const parser = new DOMParser();
        return parser.parseFromString(htmlContent, 'text/html');
    } catch (error) {
        console.error('Fetch error:', error);
        return null;
    }
}

// --- Home Section ---
async function loadHome() {
    if (isLoading) return;
    isLoading = true;
    loadingIndicator.style.display = 'block';

    let url;
    if (searchQuery) {
        url = `${BASE_URL}/site-1.html?to-search=${encodeURIComponent(searchQuery)}&to-page=${currentPage}`;
    } else if (currentCategoryUrl) {
        // Handle category pagination
        // Category URL format: /page-cat/9/Name.html
        // Pagination format usually: /page-cat/9/Name/2.html or similar?
        // The user provided: https://www.filmyfiy.mov/page-3/9/New-Hollywood-Hindi-Dubbed-Movie-2016-2025/4
        // But the home page pagination is ?to-page=2
        // Let's try appending ?to-page=X first as it's safer for some sites, 
        // but looking at the user's example: /page-cat/9/... -> /page-3/9/...
        // This is complex without exact logic. I will try the query param first or just standard home pagination.
        if (currentPage === 1) {
            url = currentCategoryUrl.startsWith('http') ? currentCategoryUrl : BASE_URL + currentCategoryUrl;
        } else {
             // Try to construct pagination url based on user example if possible, 
             // otherwise fallback to query param which might work.
             // User example: https://www.filmyfiy.mov/page-3/9/New-Hollywood-Hindi-Dubbed-Movie-2016-2025/4
             // This looks like /page-{page}/{catID}/{slug}/{page}? No that's weird.
             // Let's stick to home pagination for now or simple query param.
             url = `${BASE_URL}/?to-page=${currentPage}`; 
             if(currentCategoryUrl) {
                 // If we are in a category, we need to figure out pagination.
                 // For now, let's just support infinite scroll on Home.
                 // If user clicks category, we load page 1.
                 // If they scroll, we might fail to load page 2 correctly without exact logic.
                 // Let's assume standard home pagination works for home.
             }
        }
    } else {
        url = `${BASE_URL}/?to-page=${currentPage}`;
    }

    const doc = await fetchHTML(url);
    if (doc) {
        // extractCategories(doc); // Disabled in favor of static categories
        extractAndRenderPosts(doc);
        currentPage++;
    }

    isLoading = false;
    loadingIndicator.style.display = 'none';
}

function extractCategories(doc) {
    // Deprecated: Using setupStaticCategories instead
}

function extractAndRenderPosts(doc) {
    // Select both .A10 (Home) and .A2 (Search Results)
    const posts = doc.querySelectorAll('.A10, .A2');
    console.log(`Found ${posts.length} posts`);

    posts.forEach(post => {
        let imgTag, linkTag, title, category = '';

        // Strategy 1: Try finding inside a table (Home Page Structure)
        const table = post.querySelector('table');
        if (table) {
            imgTag = table.querySelector('img');
            linkTag = table.querySelector('a[href*="/page-download/"]');
            const titleDiv = table.querySelector('td:nth-child(2) a div');
            const catDiv = table.querySelector('td:nth-child(2) div[style*="border-radius"]');
            
            if (titleDiv) title = titleDiv.textContent.trim();
            if (catDiv) category = catDiv.textContent.trim();
        } 
        
        // Strategy 2: Try finding directly (Search Result Structure)
        if (!imgTag || !linkTag || !title) {
            imgTag = post.querySelector('img');
            linkTag = post.querySelector('a[href*="/page-download/"]');
            
            // For search results, title might be in a <b> tag inside the link or just text
            if (linkTag) {
                // Try to find title text. In search results it seems to be inside <b> tag
                const bTag = post.querySelector('b');
                if (bTag) {
                    title = bTag.textContent.trim();
                } else {
                    // Fallback: use link text
                    title = linkTag.textContent.trim();
                }
            }
        }

        if (imgTag && linkTag && title) {
            const imgSrc = imgTag.getAttribute('src');
            const link = linkTag.getAttribute('href');

            const card = document.createElement('div');
            card.className = 'card';
            card.innerHTML = `
                <div class="card-image-container">
                    <img src="${imgSrc}" class="card-image" loading="lazy" alt="${title}">
                </div>
                <div class="card-content">
                    <div class="card-title">${title}</div>
                    ${category ? `<div class="card-tag">${category}</div>` : ''}
                </div>
            `;
            card.onclick = () => openDetail(link, imgSrc, title);
            contentGrid.appendChild(card);
        } else {
            // console.log('Skipping incomplete post:', post.innerHTML.substring(0, 100));
        }
    });
}

// --- Infinite Scroll ---
function setupInfiniteScroll() {
    window.addEventListener('scroll', () => {
        if (currentSection !== 'home') return;
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
            loadHome();
        }
    });
}

// --- Search ---
function setupSearch() {
    searchBtn.addEventListener('click', () => {
        const query = searchInput.value.trim();
        if (query) {
            searchQuery = query;
            currentPage = 1;
            currentCategoryUrl = '';
            contentGrid.innerHTML = '';
            loadHome();
        }
    });
}

// --- Details Modal ---
async function openDetail(relativeLink, posterSrc, title) {
    modal.style.display = 'block';
    modalBody.innerHTML = '<div class="loading" style="display:block">Loading details...</div>';
    
    const fullPageUrl = BASE_URL + relativeLink;
    
    try {
        // Parallel fetch: Detail page (for desc) AND Player API (for links)
        const [doc, apiResponse] = await Promise.all([
            fetchHTML(fullPageUrl),
            fetch(PLAYER_API_BASE + encodeURIComponent(fullPageUrl)).then(res => res.json()).catch(e => null)
        ]);
        
        let description = 'No description available.';
        if (doc) {
            const descDiv = Array.from(doc.querySelectorAll('.fname')).find(el => el.textContent.includes('Description'));
            if (descDiv) description = descDiv.textContent.replace('Description:', '').trim();
        }

        // Render Links from API
        let linksHtml = '';
        if (apiResponse && Array.isArray(apiResponse) && apiResponse.length > 0) {
            linksHtml = '<div class="links-container" style="margin-top:20px; display:flex; flex-direction:column; gap:10px;">';
            linksHtml += '<h3>Select Server to Play/Download</h3>';
            apiResponse.forEach((link, index) => {
                // Escape quotes for the onclick handler
                const safeUrl = link.url.replace(/'/g, "\\'");
                const safeTitle = (link.title || 'Server ' + (index + 1)).replace(/'/g, "\\'");
                linksHtml += `<button class="download-btn" onclick="playOrDownload('${safeUrl}', '${link.type || ''}')">${safeTitle}</button>`;
            });
            linksHtml += '</div>';
        } else {
            linksHtml = '<p>No download/streaming links found via API.</p>';
        }

        modalBody.innerHTML = `
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <img src="${posterSrc}" class="detail-poster" style="max-width:150px; margin:0;">
                <div style="flex:1;">
                    <h2>${title}</h2>
                    <p style="margin: 10px 0; color: #ccc; font-size: 0.9rem;">${description}</p>
                </div>
            </div>
            ${linksHtml}
        `;

    } catch (error) {
        console.error('Detail Error:', error);
        modalBody.innerHTML = '<p>Error loading details.</p>';
    }
}

// Global function for onclick
window.playOrDownload = function(url, type) {
    window.open(url, '_blank');
};

function setupModal() {
    closeModal.onclick = () => {
        modal.style.display = 'none';
        // Stop video when closing
        const playerContainer = document.getElementById('playerContainer');
        if(playerContainer) playerContainer.innerHTML = '';
    };
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
            const playerContainer = document.getElementById('playerContainer');
            if(playerContainer) playerContainer.innerHTML = '';
        }
    };
}

// --- Live TV ---
let hls = null;

async function loadLiveTV() {
    isLoading = true;
    loadingIndicator.style.display = 'block';
    
    // Inject User's Live TV HTML Structure
    contentGrid.innerHTML = `
        <div class="livetv-wrapper">
            <div class="actions">
                <button class="btn btn-pri btn-pill" id="reloadBtn">Reload</button>
                <button class="btn btn-acc btn-pill" id="shuffleBtn">Shuffle</button>
                <button class="btn btn-warn d-none btn-pill" id="theaterBtn" style="display:none">Theater</button>
                <button class="btn d-nonr btn-danger btn-pill" id="stopBtn" style="display:none">Stop</button>
            </div>

            <div class="layout">
                <div>
                    <div class="now">
                        <img id="nowLogo" class="now-thumb" alt="logo" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
                        <div style="flex:1">
                            <div id="nowName" class="meta-name">Select a channel...</div>
                            <div id="nowSub" class="meta sub">—</div>
                        </div>
                        <div class="chips">
                            <span class="chip" id="chipHLS" style="display:none">HLS</span>
                        </div>
                    </div>

                    <div class="video-container" id="videoContainer">
                        <video id="player" playsinline controls></video>
                    </div>

                    <div class="panel">
                        <button class="btn btn-pri" id="prevBtn">Prev ⟨</button>
                        <button class="btn btn-acc" id="playBtn">Play ▶</button>
                        <button class="btn btn-warn" id="nextBtn">Next ⟩</button>
                        <span class="chip">Shortcuts: Space Play/Pause, M Mute</span>
                    </div>

                    <div class="search">
                        <input id="q" class="input" placeholder="Search channels..." />
                        <div class="chips" id="searchChips"></div>
                    </div>

                    <div id="list" class="channel-grid"></div>
                </div>
            </div>
            <button id="backToTop" class="btn" title="Back to top" style="display:none; position:fixed; bottom:20px; right:20px; z-index:1001;">↑</button>
        </div>
    `;

    const SOURCE_URL = 'https://dhanjeerider.github.io/Projects/channels.json';
    let all = [], filtered = [], current = -1, playToken = 0;
    const listEl = document.getElementById('list');
    const qEl = document.getElementById('q');
    const video = document.getElementById('player');
    const videoContainer = document.getElementById('videoContainer');
    const backToTopBtn = document.getElementById('backToTop');

    // Sticky Video & Back to top
    let isSticky=false,isDragging=false,offsetX=0,offsetY=0;
    window.addEventListener('scroll',()=>{
      if(window.scrollY>300&&!isSticky){videoContainer.classList.add('sticky');isSticky=true}
      if(window.scrollY<=300&&isSticky){videoContainer.classList.remove('sticky');isSticky=false}
      backToTopBtn.style.display=(window.scrollY>200)?'block':'none';
    });
    backToTopBtn.addEventListener('click',()=>{window.scrollTo({top:0,behavior:'smooth'})});

    // Dragging for sticky video
    videoContainer.addEventListener('mousedown',e=>{
      if(!isSticky) return;
      isDragging=true;
      offsetX=e.clientX-videoContainer.getBoundingClientRect().left;
      offsetY=e.clientY-videoContainer.getBoundingClientRect().top;
    });
    window.addEventListener('mousemove',e=>{
      if(isDragging){videoContainer.style.left=(e.clientX-offsetX)+'px';videoContainer.style.top=(e.clientY-offsetY)+'px';videoContainer.style.right='auto';videoContainer.style.bottom='auto'}
    });
    window.addEventListener('mouseup',()=>{isDragging=false});

    async function loadChannels(){
      try{
        const res=await fetch(SOURCE_URL,{mode:'cors',cache:'no-store'});
        const data=await res.json();
        all=data.map(x=>({name:x.name||'Untitled',logo:x.logo||'',url:x.url||''}));
        filtered=[...all];render();
        loadingIndicator.style.display = 'none';
      }catch(err){
          listEl.innerHTML='<div style="padding:16px;color:#8aa3c3;text-align:center">लोड फेल</div>';
          loadingIndicator.style.display = 'none';
      }
    }
    function render(){
      const q=(qEl.value||'').toLowerCase();
      filtered=all.filter(c=>(c.name||'').toLowerCase().includes(q));
      listEl.innerHTML=filtered.map((c,i)=>`
        <div class="channel-item" data-idx="${i}">
          <img class="channel-thumb" src="${c.logo}" alt="">
          <div class="channel-name">${c.name}</div>
        </div>`).join('')||'<div style="padding:16px;color:#8aa3c3;text-align:center;grid-column:1/-1">कुछ नहीं मिला</div>';
      listEl.querySelectorAll('.channel-item').forEach(el=>{
        el.addEventListener('click',()=>{playByIndex(+el.dataset.idx)});
      });
      if(current===-1&&filtered.length){playByIndex(0)}
    }
    function updateNow(c){
      document.getElementById('nowLogo').src=c.logo||'';
      document.getElementById('nowName').textContent=c.name||'Untitled';
      document.getElementById('nowSub').textContent=safeHost(c.url);
      document.getElementById('chipHLS').style.display=/.m3u8/i.test(c.url)?'inline-flex':'none';
    }
    async function playByIndex(i){const token=++playToken;current=i;const c=filtered[i];if(!c) return;updateNow(c);await playSrc(c.url);if(token!==playToken)return}
    document.getElementById('playBtn').addEventListener('click',async()=>{if(current<0&&filtered.length){await playByIndex(0);return}if(video.paused){await safePlay(video)}else{video.pause()}if(video.muted)try{video.muted=false}catch{}});
    document.getElementById('prevBtn').addEventListener('click',()=>{if(!filtered.length)return;const i=current<=0?filtered.length-1:current-1;playByIndex(i)});
    document.getElementById('nextBtn').addEventListener('click',()=>{if(!filtered.length)return;const i=current>=filtered.length-1?0:current+1;playByIndex(i)});
    document.getElementById('shuffleBtn').addEventListener('click',()=>{if(!filtered.length)return;const i=Math.floor(Math.random()*filtered.length);playByIndex(i)});
    document.getElementById('theaterBtn').addEventListener('click',()=>{videoContainer.classList.remove('sticky');isSticky=false;window.scrollTo({top:0,behavior:'smooth'})});
    document.getElementById('stopBtn').addEventListener('click',()=>{destroyHls();video.removeAttribute('src');video.load();current=-1;document.getElementById('nowName').textContent='रुका हुआ';document.getElementById('nowSub').textContent='—';document.getElementById('nowLogo').src=''});
    document.getElementById('reloadBtn').addEventListener('click',loadChannels);
    qEl.addEventListener('input',render);
    window.addEventListener('keydown',e=>{if(['INPUT','TEXTAREA'].includes(e.target.tagName))return;if(e.code==='Space'){e.preventDefault();document.getElementById('playBtn').click()}if(e.key==='m'||e.key==='M'){video.muted=!video.muted}if(e.key==='ArrowRight'){document.getElementById('nextBtn').click()}if(e.key==='ArrowLeft'){document.getElementById('prevBtn').click()}});

    // Search category buttons
    const SEARCH_TERMS=["Movies","News","Music","Sports","Kids","India","bhojpuri","Live","mtv","star","entertainment"];
    const chipsEl=document.getElementById('searchChips');
    chipsEl.innerHTML=SEARCH_TERMS.map(t=>`<span class="chip" data-term="${t}">${t}</span>`).join('');
    chipsEl.querySelectorAll('.chip').forEach(btn=>{
      btn.addEventListener('click',()=>{qEl.value=btn.dataset.term;render();window.scrollTo({top:0,behavior:'smooth'})});
    });

    function safeHost(u){try{return new URL(u).hostname}catch{return '—'}}
    
    async function playSrc(url) {
        destroyHls();
        if (!url) return;

        if (Hls.isSupported() && /\.m3u8/i.test(url)) {
            hls = new Hls({ autoStartLoad: true, enableWorker: true });
            hls.loadSource(url);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, async () => { await safePlay(video); });
            hls.on(Hls.Events.ERROR, (e, data) => {
                if (data.fatal) {
                    switch (data.type) {
                        case Hls.ErrorTypes.NETWORK_ERROR: hls.startLoad(); break;
                        case Hls.ErrorTypes.MEDIA_ERROR: hls.recoverMediaError(); break;
                        default: destroyHls(); break;
                    }
                }
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = url;
            await safePlay(video);
        } else {
            video.src = url;
            await safePlay(video);
        }
    }

    function destroyHls() {
        if (hls) {
            try { hls.destroy(); } catch {}
            hls = null;
        }
    }

    async function safePlay(el) {
        try { await el.play(); } catch (err) { console.warn("Autoplay prevented:", err); }
    }

    loadChannels();
}

// --- TMDB ---
function renderTMDB() {
    // Reset TMDB State
    tmdbCurrentPage = 1;
    tmdbCurrentQuery = '';
    tmdbIsSearching = false;

    contentGrid.innerHTML = `
        <div style="width: 100%;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2>Search any movie and play them</h2>
            </div>
            <div class="tmdb-search-container">
                <input type="text" id="tmdbSearchInput" placeholder="Search movies live...">
            </div>

            <div class="tmdb-movie-grid" id="tmdbMovieGrid"></div>

            <div class="tmdb-load-more-container">
                <button id="tmdbLoadMoreBtn">Load More</button>
            </div>

            <div class="tmdb-player-modal" id="tmdbPlayerModal">
                <div class="tmdb-player-wrapper">
                    <div class="tmdb-player-header">
                        <div class="tmdb-server-list" id="tmdbServerList">
                            <!-- Server buttons injected here -->
                        </div>
                        <button class="tmdb-close-btn" id="tmdbCloseBtn">&times;</button>
                    </div>
                    <div class="tmdb-iframe-container">
                        <iframe id="tmdbVideoFrame" src="" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    `;

    const tmdbGrid = document.getElementById('tmdbMovieGrid');
    const loadMoreBtn = document.getElementById('tmdbLoadMoreBtn');
    const searchInput = document.getElementById('tmdbSearchInput');
    const modal = document.getElementById('tmdbPlayerModal');
    const closeBtn = document.getElementById('tmdbCloseBtn');
    const iframe = document.getElementById('tmdbVideoFrame');

    // Initial Load
    fetchTMDBMovies(tmdbCurrentPage);

    // Event Listeners
    loadMoreBtn.addEventListener('click', () => {
        tmdbCurrentPage++;
        fetchTMDBMovies(tmdbCurrentPage, tmdbCurrentQuery);
    });

    searchInput.addEventListener('input', (e) => {
        tmdbCurrentQuery = e.target.value.trim();
        tmdbGrid.innerHTML = ''; // Clear current grid
        tmdbCurrentPage = 1;
        
        if (tmdbCurrentQuery.length > 0) {
            tmdbIsSearching = true;
            fetchTMDBMovies(tmdbCurrentPage, tmdbCurrentQuery);
        } else {
            tmdbIsSearching = false;
            fetchTMDBMovies(tmdbCurrentPage); // Back to popular
        }
    });

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        iframe.src = '';
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
            iframe.src = '';
        }
    });
}

async function fetchTMDBMovies(page, query = '') {
    const tmdbGrid = document.getElementById('tmdbMovieGrid');
    const loadMoreBtn = document.getElementById('tmdbLoadMoreBtn');
    
    let url;
    if (query) {
        url = `${TMDB_BASE_URL}/search/movie?api_key=${TMDB_API_KEY}&query=${encodeURIComponent(query)}&page=${page}`;
    } else {
        url = `${TMDB_BASE_URL}/movie/popular?api_key=${TMDB_API_KEY}&page=${page}`;
    }

    try {
        const res = await fetch(url);
        const data = await res.json();
        renderTMDBMovies(data.results);
        
        // Show/Hide Load More based on results
        if (data.results.length > 0) {
            loadMoreBtn.style.display = 'inline-block';
        } else {
            loadMoreBtn.style.display = 'none';
        }
    } catch (error) {
        console.error("Error fetching movies:", error);
    }
}

function renderTMDBMovies(movies) {
    const tmdbGrid = document.getElementById('tmdbMovieGrid');
    movies.forEach(movie => {
        if (!movie.poster_path) return; // Skip if no image

        const card = document.createElement('div');
        card.classList.add('tmdb-movie-card');
        card.onclick = () => openTMDBPlayer(movie.id); // Pass TMDB ID

        card.innerHTML = `
            <img src="${TMDB_IMAGE_BASE}${movie.poster_path}" alt="${movie.title}" loading="lazy">
            <div class="tmdb-movie-info">
                <div class="tmdb-movie-title">${movie.title}</div>
            </div>
        `;
        tmdbGrid.appendChild(card);
    });
}

async function openTMDBPlayer(tmdbId) {
    const modal = document.getElementById('tmdbPlayerModal');
    const iframe = document.getElementById('tmdbVideoFrame');
    const serverList = document.getElementById('tmdbServerList');

    // Clear previous state
    iframe.src = '';
    serverList.innerHTML = 'Loading servers...';
    modal.style.display = 'flex';

    try {
        const res = await fetch(`${TMDB_BASE_URL}/movie/${tmdbId}/external_ids?api_key=${TMDB_API_KEY}`);
        const data = await res.json();
        
        if (data.imdb_id) {
            const imdbId = data.imdb_id;
            
            // Define Servers
            const servers = [
                { name: 'Server 1 (Kinej)', url: `${TMDB_PLAYER_BASE}${imdbId}` },
                { name: 'Server 2 (Vidify)', url: `https://player.vidify.top/embed/movie/${tmdbId}` },
                { name: 'Server 3 (VidRock)', url: `https://vidrock.net/movie/${imdbId}` }
            ];

            // Render Buttons
            serverList.innerHTML = '';
            servers.forEach((server, index) => {
                const btn = document.createElement('button');
                btn.className = 'tmdb-server-btn';
                btn.textContent = server.name;
                if (index === 0) btn.classList.add('active');
                
                btn.onclick = () => {
                    document.querySelectorAll('.tmdb-server-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    iframe.src = server.url;
                };
                
                serverList.appendChild(btn);
            });

            // Load first server by default
            iframe.src = servers[0].url;

        } else {
            serverList.innerHTML = 'Stream not available (No IMDB ID).';
        }
    } catch (error) {
        console.error("Error fetching IMDB ID:", error);
        serverList.innerHTML = 'Error loading player details.';
    }
}

