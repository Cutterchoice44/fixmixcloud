// script_IND_DJ.js
const API_KEY      = "pk_0b8abc6f834b444f949f727e88a728e0";
const BASE_URL     = "https://api.radiocult.fm/api";
const STATION_ID   = "cutters-choice-radio";
const MIXCLOUD_PW  = "cutters44";
const FALLBACK_ART = "https://i.imgur.com/qWOfxOS.png";

// Server endpoints (adjust paths if needed)
const GET_ARCHIVES_URL   = "get_archives.php";
const ADD_ARCHIVE_URL    = "add_archive.php";
const DELETE_ARCHIVE_URL = "delete_archive.php";

// Helpers
function showNotFound() {
  document.querySelector(".profile-wrapper").innerHTML = `
    <p style="color:white;text-align:center;margin:2rem;">
      Profile not found.
    </p>`;
}
function showError() {
  document.querySelector(".profile-wrapper").innerHTML = `
    <p style="color:white;text-align:center;margin:2rem;">
      Error loading profile.
    </p>`;
}
function createGoogleCalLink(title, s, e) {
  if (!s||!e) return "#";
  const fmt = dt=>new Date(dt).toISOString().replace(/-|:|\.\d{3}/g,'');
  return `https://www.google.com/calendar/render?action=TEMPLATE`
       + `&text=${encodeURIComponent(title)}`
       + `&dates=${fmt(s)}/${fmt(e)}`;
}

async function initPage() {
  const params   = new URLSearchParams(location.search);
  const artistId = params.get("id");
  if (!artistId) return showNotFound();

  // 1–7) Artist fetch, tag check, name, bio, artwork, socials, calendar (unchanged)
  // … (copy your existing logic here) …

  // 8) Mixcloud archive persistence via server
  const listEl = document.getElementById("mixes-list");

  async function loadShows() {
    listEl.innerHTML = "";
    try {
      const res = await fetch(`${GET_ARCHIVES_URL}?artistId=${encodeURIComponent(artistId)}`);
      if (!res.ok) throw new Error(`Load archives ${res.status}`);
      const archives = await res.json();
      archives.forEach(entry => {
        if (!entry.url.includes(`/${artistId}/`)) return;
        const url = entry.url;
        const wrapper = document.createElement("div");
        wrapper.className = "mix-show";

        const iframe = document.createElement("iframe");
        iframe.width      = "100%";
        iframe.height     = "60";
        iframe.frameBorder= "0";
        iframe.allow      = "autoplay";
        iframe.src = "https://www.mixcloud.com/widget/iframe/?hide_cover=1&light=1&feed=" + encodeURIComponent(url);
        wrapper.appendChild(iframe);

        const btn = document.createElement("button");
        btn.textContent = "Remove show";
        btn.onclick = async () => {
          const pwd = prompt("Enter password to remove this show:");
          if (pwd !== MIXCLOUD_PW) return alert("Incorrect password");
          try {
            const delRes = await fetch(DELETE_ARCHIVE_URL, {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: new URLSearchParams({ artistId, url })
            });
            if (!delRes.ok) throw new Error(`Delete ${delRes.status}`);
            await loadShows();
          } catch (e) {
            console.error(e);
            alert("Error removing show.");
          }
        };
        wrapper.appendChild(btn);

        listEl.appendChild(wrapper);
      });
    } catch (e) {
      console.error(e);
      listEl.innerHTML = `<p style="color:white;text-align:center;margin:2rem;">Error loading shows.</p>`;
    }
  }

  loadShows();

  document.getElementById("add-show-btn").onclick = async () => {
    const pwd = prompt("Enter password to add a show:");
    if (pwd !== MIXCLOUD_PW) return alert("Incorrect password");
    const input = document.getElementById("mixcloud-url-input");
    const u = input.value.trim();
    if (!u) return;
    try {
      const addRes = await fetch(ADD_ARCHIVE_URL, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ artistId, url: u })
      });
      if (!addRes.ok) throw new Error(`Add ${addRes.status}`);
      input.value = "";
      await loadShows();
    } catch (e) {
      console.error(e);
      alert("Error adding show.");
    }
  };
}

window.addEventListener("DOMContentLoaded", initPage);
