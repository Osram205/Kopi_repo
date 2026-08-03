export type RootStackParamList = {
  Login: undefined;
  Main: undefined;
  PublishTrip: undefined;
  AddVehicle: undefined;
};

export type BottomTabParamList = {
  Feed: undefined;
  PublishTrip: undefined;
  Profile: undefined;
};

export type { Viaje } from '../types/trip.types';

export type FeedStackParamList = {
  FeedList: undefined;
  FeedDetail: Viaje;
  Tracking: Viaje;
  Rating: Viaje;
};
