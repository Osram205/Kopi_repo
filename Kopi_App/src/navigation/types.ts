export type RootStackParamList = {
  Login: undefined;
  Main: undefined;
};

export type BottomTabParamList = {
  Feed: undefined;
  Profile: undefined;
};

export type Viaje = {
  id: string;
  destino: string;
  precio: string;
  conductor: {
    nombre: string;
    calificacion: string;
  };
  vehiculo: {
    marca: string;
    placa: string;
  };
  paradas?: string[];
  imageUrl: string;
  description: string;
};

export type FeedStackParamList = {
  FeedList: undefined;
  FeedDetail: Viaje;
  Tracking: Viaje;
  Rating: Viaje;
};
