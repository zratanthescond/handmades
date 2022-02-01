/**
 * 

document.addEventListener("DOMContentLoaded", () => {
  const addresses = document.querySelectorAll(".client_address_edit");

  if (addresses.length) {

    console.log("yes")

    const modal = new Modal(document.getElementById("addressModal"), {
        keyboard: false,
      });
   

    addresses.forEach((address) => {
      const id = address.dataset.addressId;

      const clientAdressEl = address.querySelector(".client_address");

      const btn = address.querySelector(".edit_btn");

      btn.addEventListener("click", () => {
        //modal.show();
      });
    });
  }
});

 */