import React from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, Image, SafeAreaView } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { FeedStackParamList, Viaje } from '../navigation/types';

type Props = NativeStackScreenProps<FeedStackParamList, 'FeedList'>;

const DATA: Viaje[] = [
  { 
    id: '1', 
    destino: 'Buenos Aires, Argentina', 
    precio: '$1,200',
    conductor: { nombre: 'Juan Perez', calificacion: '4.9' },
    vehiculo: { marca: 'Toyota Corolla', placa: 'AB-123-CD' },
    paradas: ['Rosario', 'Campana'],
    description: 'Viaje tranquilo hacia Buenos Aires, saliendo temprano.',
    imageUrl: 'https://images.unsplash.com/photo-1544463878-a3f29bb5ebfb?q=80&w=600&auto=format&fit=crop'
  },
  { 
    id: '2', 
    destino: 'Cordoba, Argentina', 
    precio: '$2,500',
    conductor: { nombre: 'Maria Gomez', calificacion: '4.8' },
    vehiculo: { marca: 'Honda Civic', placa: 'UMK-987' },
    description: 'Viaje a Cordoba, con buena música y aire acondicionado.',
    imageUrl: 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=600&auto=format&fit=crop'
  }
];

export default function FeedScreen({ navigation }: Props) {
  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        <View style={styles.header}>
          <Text style={styles.headerTitle}>Viajes Disponibles</Text>
          <Text style={styles.headerSubtitle}>Encuentra tu próximo viaje</Text>
        </View>

        <FlatList
          data={DATA}
          keyExtractor={item => item.id}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.listContainer}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={styles.card}
              activeOpacity={0.8}
              onPress={() => navigation.navigate('FeedDetail', item)}
            >
              <Image source={{ uri: item.imageUrl }} style={styles.cardImage} />
              <View style={styles.cardOverlay}>
                <View style={styles.ratingBadge}>
                  <Text style={styles.ratingText}>★ {item.conductor.calificacion}</Text>
                </View>
              </View>
              <View style={styles.cardContent}>
                <View style={styles.titleRow}>
                  <Text style={styles.cardTitle}>{item.destino}</Text>
                  <Text style={styles.cardPrice}>{item.precio}</Text>
                </View>
                <Text style={styles.cardLocation}>🚗 Conductor: {item.conductor.nombre} - {item.vehiculo.marca}</Text>
              </View>
            </TouchableOpacity>
          )}
        />
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#09090B',
  },
  container: {
    flex: 1,
  },
  header: {
    paddingHorizontal: 24,
    paddingTop: 16,
    paddingBottom: 24,
  },
  headerTitle: {
    fontSize: 32,
    fontWeight: '800',
    color: '#FFFFFF',
    marginBottom: 4,
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#A1A1AA',
  },
  listContainer: {
    paddingHorizontal: 16,
    paddingBottom: 24,
  },
  card: {
    backgroundColor: '#18181B',
    borderRadius: 24,
    marginBottom: 24,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#27272A',
  },
  cardImage: {
    width: '100%',
    height: 180,
  },
  cardOverlay: {
    position: 'absolute',
    top: 16,
    right: 16,
  },
  ratingBadge: {
    backgroundColor: 'rgba(9, 9, 11, 0.7)',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 12,
  },
  ratingText: {
    color: '#FBBF24',
    fontWeight: 'bold',
    fontSize: 14,
  },
  cardContent: {
    padding: 20,
  },
  titleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  cardTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: '#FFFFFF',
    flex: 1,
    paddingRight: 16,
  },
  cardPrice: {
    fontSize: 18,
    fontWeight: '800',
    color: '#FBBF24',
  },
  cardLocation: {
    fontSize: 14,
    color: '#A1A1AA',
    fontWeight: '500',
  }
});
