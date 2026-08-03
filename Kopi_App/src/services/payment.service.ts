import { apiClient } from '../api/client';

export interface CheckoutRequest {
  reservacion_id: number;
  metodo_pago: 'tarjeta';
}

export interface CheckoutResponse {
  checkout_url: string;
}

export interface ConfirmPaymentRequest {
  reservacion_id: number;
  metodo_pago: 'tarjeta';
  session_id: string;
}

export const PaymentService = {
  /**
   * Crea una sesión de checkout en Stripe.
   */
  async createCheckout(data: CheckoutRequest): Promise<CheckoutResponse> {
    const response = await apiClient.post<CheckoutResponse>('/pagos/checkout', data);
    return response.data;
  },

  /**
   * Confirma un pago realizado con éxito en Stripe.
   */
  async confirmPayment(data: ConfirmPaymentRequest): Promise<any> {
    const response = await apiClient.post<any>('/pagos/confirmar', data);
    return response.data;
  }
};
