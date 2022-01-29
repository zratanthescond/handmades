import axios from "axios";

document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("create_shipment");

  const errorEl = document.getElementById("create_shipment_error");

  if (!btn || !errorEl) return;

  btn.addEventListener("click", async () => {
    const token = btn.dataset.token,
      orderId = btn.dataset.orderId;

    btn.setAttribute("disabled", "disabled");

    try {
      const res = await axios.post("/aramex/create/shippement", {
        token,
        orderId,
      });

      const { trackingId } = res.data;

      document.getElementById("trackingId").innerHTML = `Aramex Tracking Id: ${trackingId}`;

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
