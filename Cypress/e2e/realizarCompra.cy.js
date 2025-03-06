describe('Registro y redirección', () => {
  it('Registrar usuario y verificar en pantalla principal', () => {
    cy.visit('http://localhost/');

    cy.get('#menu-hamburguer').click();
    cy.contains('Registrarse').click();
    cy.get('#selectEntidad').select('Cliente');
    cy.get('#name').type('Alvaro');
    cy.get('#email').type('Alvarocorreo232@gmail.com');
    cy.get('#password').type('Alvaro1234');
    cy.get('#dni').type('49450649P');
    cy.get('#foto_dni').attachFile('foto.jpg');
    cy.contains('Crear Cliente').click();
    cy.get('#foto-flamenco').click();
    cy.get('#comprar').click();
    cy.get('#carrito').click();
    cy.contains('Ver Carrito').click();
    cy.get('#pagar').click();

    cy.get('iframe[src*="checkout.stripe.com"]').then(($iframe) => {
      const body = $iframe.contents().find('body');
      cy.wrap(body).within(() => {
        cy.get('input[name="email"]').type('Alvarocorreo1@gmail.com');
        cy.get('input[type="checkbox"]').check({ force: true }); 
        cy.get('button[type="submit"]').click();
      });
    });
  });
});
