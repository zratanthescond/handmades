import axios from "axios";

document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("update_shippement");

  const errorEl = document.getElementById("update_shippement_error");

  if (!btn || !errorEl) return;

  btn.addEventListener("click", async () => {
    const trackingId = btn.dataset.trackingId;

    btn.setAttribute("disabled", "disabled");

    try {
      const res = await axios.post("/aramex/track/shippement", {
        trackingIds: [trackingId],
      });

      const { data: {UpdateDescription} } = res.data[0];

      document.getElementById("tracking_status").innerHTML = UpdateDescription;

      btn.remove();
    } catch (e) {
      if (e.response) {
        const { error } = e.response.data;

        errorEl.innerHTML = error;
      } else {
        errorEl.innerHTML = "Une erreur inconnue est produite !";
      }
      btn.remove();
      errorEl.style.display = "block";
    }
  });
});
